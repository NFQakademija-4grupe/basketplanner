<?php

namespace BasketPlanner\MatchBundle\Services;

use BasketPlanner\MatchBundle\Entity\Match;
use BasketPlanner\MatchBundle\Form\FilterType;
use BasketPlanner\MatchBundle\Form\MatchType;
use BasketPlanner\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Paginator;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;


class MatchLoaderService
{
    const MATCHES_PER_PAGE = 8;

    private $em;

    private $formFactory;

    private $paginator;

    public function __construct(EntityManager $entityManager, FormFactory $formFactory, Paginator $paginator)
    {
        $this->em = $entityManager;
        $this->formFactory = $formFactory;
        $this->paginator = $paginator;
    }

    public function loadMatchesAndForm(Request $request)
    {
        $qb = $this->em->getRepository('BasketPlannerMatchBundle:Match')->createQueryBuilder('m');

        $filterForm = $this->formFactory->create(new FilterType());
        $filterForm->handleRequest($request);

        $query = $qb->select('m');

        // build query if filter form is submitted
        if ($filterForm->isSubmitted())
        {
            $formData = $filterForm->getData();

            if (!is_null($formData['search_text'])) {
                $query = $query->andWhere('m.description LIKE :searchText')
                    ->setParameter('searchText', '%'.$formData['search_text'].'%');
            }

            if (!$formData['type']->isEmpty()) {
                $query = $query->andWhere('m.type IN (:type)')
                    ->setParameter('type', $formData['type']->toArray());
            }

            if (!is_null($formData['min_date']) && !is_null($formData['max_date']))
            {
                $query = $query->andWhere('m.beginsAt BETWEEN :minDate AND :maxDate')
                    ->setParameter('minDate', $formData['min_date'])
                    ->setParameter('maxDate', $formData['max_date']);
            }
            else if (!is_null($formData['min_date']))
            {
                $query = $query->andWhere('m.beginsAt > :minDate')
                    ->setParameter('minDate', $formData['min_date']);
            }
            else if (!is_null($formData['max_date']))
            {
                $query = $query->andWhere('m.beginsAt < :maxDate')
                    ->setParameter('maxDate', $formData['max_date']);
            }
        }

        $query = $query
            ->andWhere('m.active = :active')
            ->setParameter('active', true)
            ->orderBy('m.beginsAt')
            ->getQuery();

        $pagination = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', $request->get('page')),
            self::MATCHES_PER_PAGE
        );

        return [
            'pagination' => $pagination,
            'form' => $filterForm->createView()
        ];
    }

    public function saveMatch(Request $request, User $user)
    {
        $match = new Match();

        $form = $this->formFactory->create(new MatchType(), $match);

        $form->handleRequest($request);

        $results = [
            'matchSaved' => false,
            'form' => $form->createView()
        ];

        if ($form->isValid())
        {
            $courtId = $form['court']->getData()->getId();

            $court = $this->em->getRepository('BasketPlannerMatchBundle:Court')->findOneBy(['id' => $courtId]);

            if (is_null($court)) {
                $court = $match->getCourt();
                $court->setApproved(false);
                $this->em->persist($court);
                $this->em->flush();
            }

            $match->setOwner($user);
            $match->addPlayer($user);
            $match->setPlayersCount(1);
            $match->setCourt($court);
            $match->setActive(true);
            $match->setCreatedAt(new \DateTime('now'));

            $this->em->persist($match);
            $this->em->flush();

            $results['match'] = $match;
            $results['matchSaved'] = true;
        }

        return $results;
    }

    public function getLatest($num)
    {
        $qb = $this->em->getRepository('BasketPlannerMatchBundle:Match')->createQueryBuilder('m');
        $results = $qb->setMaxResults($num)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $results;
    }
}