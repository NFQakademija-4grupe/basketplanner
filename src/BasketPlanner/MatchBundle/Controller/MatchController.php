<?php
// TODO: ar gali kurti maca, jei yra prisijunges prie maco
// TODO: ar gali prisijungti prie maco, jei yra sukures maca
namespace BasketPlanner\MatchBundle\Controller;

use BasketPlanner\MatchBundle\Entity\Match;
use BasketPlanner\MatchBundle\Form\MatchType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ivory\GoogleMap\Overlays\Marker;
use Ivory\GoogleMap\Overlays\InfoWindow;
use Ivory\GoogleMap\Helper\MapHelper;

class MatchController extends Controller
{
    /**
     * @var Ivory\GoogleMapBundle\Model\Map
     */
    public function indexAction()
    {
        $loadMap = $this->get('basket_planner_match.map_loader_service');
        $map = $loadMap->loadMarkers(false);
        $mapVariable = $map->getJavascriptVariable();
        return $this->render('BasketPlannerMatchBundle:Match:index.html.twig', array(
            'map' => $map,
            'mapVariable' => $mapVariable
        ));
    }

    /**
     * Show all matches
     *
     * @param Request $request
     * @param int $page
     * @return Response
     */
    public function listAction(Request $request, $page)
    {
        $repository = $this->getDoctrine()->getEntityManager()->getRepository('BasketPlannerMatchBundle:Match');

        $query = $repository->createQueryBuilder('m')
            ->select('m')
            ->where('m.active = :active')
            ->setParameter('active', true)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery();

        $pagination = $this->get('knp_paginator')->paginate(
            $query,
            $request->query->getInt('page', $page),
            9
        );

        return $this->render('BasketPlannerMatchBundle:Match:list.html.twig', array('pagination' => $pagination));
    }

    /**
     * Show individual match
     *
     * @param Match $match
     * @return Response
     */
    public function showAction(Match $match)
    {
        return $this->render('BasketPlannerMatchBundle:Match:show.html.twig', ['match' => $match]);
    }

    /**
     * Show match form/create match if form is submitted
     *
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $loadMap = $this->get('basket_planner_match.map_loader_service');
        $map = $loadMap->loadMarkers(false);
        $mapVariable = $map->getJavascriptVariable();

        $match = new Match();

        $form = $this->createForm(new MatchType(), $match);

        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isValid())
        {
            $user = $this->getUser();

            $match->setOwner($user);
            $match->addPlayer($user);
            $match->setPlayersCount(1);
            $match->setActive(true);
            $match->setCreatedAt(new \DateTime('now'));

            $em->persist($match);
            $em->flush();

            return $this->redirectToRoute('basket_planner_match_show', ['id' => $match->getId()]);
        }

        return $this->render('BasketPlannerMatchBundle:Match:create.html.twig', [
            'form' => $form->createView(),
            'map' => $map,
            'mapVariable' => $mapVariable,
        ]);
    }

    /**
     * Edit match
     *
     * @param Request $request
     * @param Match $match
     * @return Response
     */
    public function editAction(Request $request, Match $match)
    {
        if ($match->getOwner() !== $this->getUser())
        {
            $this->addFlash('error', 'Mačo aprašymą gali redaguoti tik jį sukūręs vartotojas');
            return $this->redirectToRoute('basket_planner_match_show', ['id' => $match->getId()]);
        }

        $em =  $this->getDoctrine()->getManager();

        $form = $this->createForm(new MatchType(), $match, ['for_editing' => true]);

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em->flush();
            $this->addFlash('success', 'Mačo informacija sėkmingai pakeista!');
            return $this->redirectToRoute('basket_planner_match_show', ['id' => $match->getId()]);
        }

        $courts = $em->getRepository('BasketPlannerMatchBundle:Court')->findByApproved(1);

        return $this->render('BasketPlannerMatchBundle:Match:create.html.twig',
            ['form' => $form->createView(), 'courts' => $courts]);
    }

    /**
     * Join match
     *
     * @param Match $match
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function joinAction(Match $match)
    {
        if ($match->getPlayersCount() < $match->getType()->getPlayers())
        {
            try
            {
                $match->addPlayer($this->getUser());
                $match->increasePlayersCount();

                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', 'Sėkmingai prisijungėte prie mačo!');

            } catch (UniqueConstraintViolationException $ex)
            {
                $this->addFlash('error', 'Jūs jau esate prisijunge prie šio mačo');
            }
        }
        else
        {
            $this->addFlash('error', 'Prie mačo prisijungti negalima. Surinktas reikimas žaidėjų skaičiu.');
            return $this->redirectToRoute('basket_planner_match_list');
        }

        return $this->redirectToRoute('basket_planner_match_show', ['id' => $match->getId()]);
    }
}