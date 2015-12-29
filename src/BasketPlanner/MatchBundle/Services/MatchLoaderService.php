<?php

namespace BasketPlanner\MatchBundle\Services;

use BasketPlanner\MatchBundle\Entity\Match;
use BasketPlanner\MatchBundle\Entity\MatchUser;
use BasketPlanner\MatchBundle\Form\FilterType;
use BasketPlanner\MatchBundle\Form\MatchType;
use BasketPlanner\UserBundle\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Knp\Component\Pager\Paginator;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use BasketPlanner\UserBundle\Service\NotificationService;
use Symfony\Component\HttpFoundation\Session\Session;


class MatchLoaderService
{
    const MATCHES_PER_PAGE = 8;

    private $em;

    private $formFactory;

    private $paginator;

    private $session;

    public function __construct(EntityManager $em, FormFactory $formFactory, Paginator $paginator, Session $session)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->paginator = $paginator;
        $this->session = $session;
    }

    public function loadMatchesAndForm(Request $request)
    {
        $qb = $this->em->getRepository('BasketPlannerMatchBundle:Match')->createQueryBuilder('m');

        $filterForm = $this->formFactory->createNamed('', new FilterType());
        $filterForm->handleRequest($request);

        $query = $qb->select('m');

        // build query if filter form is submitted
        if ($filterForm->isSubmitted())
        {
            $formData = $filterForm->getData();

            if (!is_null($formData['search_text'])) {
                $query = $query->leftJoin('m.court', 'c', Join::WITH, 'm.court = c.id')
                    ->andWhere('m.description LIKE :searchText')
                    ->orWhere('c.address LIKE :searchText')
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
            $match->setPlayersCount(1);
            $match->setCourt($court);
            $match->setActive(true);
            $match->setCreatedAt(new \DateTime('now'));
            $match->setNotified(0);

            $player = new MatchUser();
            $player->setUser($user);
            $player->setMatch($match);

            $match->addPlayer($player);

            $this->em->persist($match);
            $this->em->persist($player);
            $this->em->flush();

            $this->session->getFlashBag()->add('success', 'Sėkmingai sukurtas mačas!');

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

    public function joinMatch(Match $match, User $user, NotificationService $notificationService)
    {
        if ($match->getPlayersCount() < $match->getType()->getPlayers())
        {
            try
            {
                $player = new MatchUser();
                $player->setUser($user);
                $player->setMatch($match);
                $match->addPlayer($player);
                $match->increasePlayersCount();

                $this->em->persist($match);
                $this->em->persist($player);
                $this->em->flush();

                $this->session->getFlashBag()->add('success', 'Sėkmingai prisijungėte prie mačo!');

                $full = false;
                if($match->getPlayersCount() == $match->getType()->getPlayers()) {
                    $full = true;
                }

                $notificationService->matchJoinNotification($match->getId(), $user->getId(), $full);
            }
            catch (UniqueConstraintViolationException $ex)
            {
                $this->session->getFlashBag()->add('error', 'Jūs jau esate prisijungę prie šio mačo');
            }
        }
        else
        {
            $this->session->getFlashBag()->add('error', 'Prie mačo prisijungti negalima. Surinktas reikiamas žaidėjų skaičiu.');
            return false;
        }

        return true;
    }

    public function leaveMatch(Match $match, User $user)
    {
        $player = $this->em->getRepository('BasketPlannerMatchBundle:MatchUser')->findOneBy(array('match' => $match, 'user' =>$user));

        if (!$match->getPlayers()->contains($player)) {
            $this->session->getFlashBag()->add('error', 'Neįmanoma išeiti iš mačo prie kurio nesate prisijunge!');
            return false;
        }

        $match->removePlayer($player);
        $match->decreasePlayersCount();

        if ($match->getPlayersCount() == 0) {
            $match->setActive(false);
        }

        $this->em->persist($match);
        $this->em->remove($player);
        $this->em->flush();

        $this->session->getFlashBag()->add('success', 'Sėkmingai išėjote iš mačo');

        return true;
    }
}