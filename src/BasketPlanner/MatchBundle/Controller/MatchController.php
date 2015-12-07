<?php
namespace BasketPlanner\MatchBundle\Controller;

use BasketPlanner\MatchBundle\Entity\Match;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MatchController extends Controller
{
    /**
     * Show all matches
     *
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        $matchLoader = $this->get('basketplanner_match.match_loader_service');
        $loadedData = $matchLoader->loadMatchesAndForm($request);

        return $this->render('BasketPlannerMatchBundle:Match:list.html.twig', $loadedData);
    }

    /**
     * Show individual match
     *
     * @param Match $match
     * @return Response
     */
    public function showAction(Match $match)
    {
        $loadMap = $this->get('basketplanner_match.map_loader_service');
        $map = $loadMap->loadMarkerById($match->getCourt()->getId());

        return $this->render('BasketPlannerMatchBundle:Match:show.html.twig', ['match' => $match, 'map' => $map]);
    }

    /**
     * Show match form and create match if form is submitted
     *
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $matchLoader = $this->get('basketplanner_match.match_loader_service');
        $data = $matchLoader->saveMatch($request, $this->getUser());

        if ($data['matchSaved']) {
            return $this->redirectToRoute('basket_planner_match_show', ['id' => $data['match']->getId()]);
        }

        $loadMap = $this->get('basketplanner_match.map_loader_service');
        $map = $loadMap->loadMarkers(true);

        return $this->render('BasketPlannerMatchBundle:Match:create.html.twig', [ 'form' => $data['form'], 'map' => $map]);
    }

    /**
     * Join match
     *
     * @param Match $match
     * @return RedirectResponse
     */
    public function joinAction(Match $match)
    {
        $notificationService = $this->get('basketplanner_user.notifications_service');
        $matchLoader = $this->get('basketplanner_match.match_loader_service');
        $joined = $matchLoader->joinMatch($match, $this->getUser(), $notificationService);

        if (!$joined) {
            return $this->redirectToRoute('basket_planner_match_list');
        }

        return $this->redirectToRoute('basket_planner_match_show', ['id' => $match->getId()]);
    }

    /**
     * Leave match
     *
     * @param Match $match
     * @return RedirectResponse
     */
    public function leaveAction(Match $match)
    {
        $matchLoader = $this->get('basketplanner_match.match_loader_service');
        $left = $matchLoader->leaveMatch($match, $this->getUser());

        if (!$left) {
            return $this->redirectToRoute('basket_planner_match_show', ['id' => $match->getId()]);
        }

        return $this->redirectToRoute('basket_planner_match_list');
    }
}