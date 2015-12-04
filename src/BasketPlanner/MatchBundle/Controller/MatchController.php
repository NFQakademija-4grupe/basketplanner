<?php
namespace BasketPlanner\MatchBundle\Controller;

use BasketPlanner\MatchBundle\Entity\Match;
use BasketPlanner\MatchBundle\Entity\Court;
use BasketPlanner\MatchBundle\Form\MatchType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ivory\GoogleMap\Overlays\Marker;
use Ivory\GoogleMap\Overlays\InfoWindow;
use Ivory\GoogleMap\Helper\MapHelper;

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
        $loadMap = $this->get('basketplanner_match.map_loader_service');
        $map = $loadMap->loadMarkers(false);

        $matchLoader = $this->get('basketplanner_match.match_loader_service');
        $data = $matchLoader->saveMatch($request, $this->getUser());

        if ($data['matchSaved']) {
            $this->addFlash('success', 'Sėkmingai sukurtas mačas!');
            return $this->redirectToRoute('basket_planner_match_show', ['id' => $data['match']->getId()]);
        }

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
        if ($match->getPlayersCount() < $match->getType()->getPlayers())
        {
            try
            {
                $match->addPlayer($this->getUser());
                $match->increasePlayersCount();

                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', 'Sėkmingai prisijungėte prie mačo!');

                $full = false;
                if($match->getPlayersCount() == $match->getType()->getPlayers()){
                    $full = true;
                }

                $notificationService = $this->get('basketplanner_user.notifications_service');
                $notificationService->matchJoinNotification($match->getId(), $this->getUser()->getId(), $full);

            } catch (UniqueConstraintViolationException $ex)
            {
                $this->addFlash('error', 'Jūs jau esate prisijungę prie šio mačo');
            }
        }
        else
        {
            $this->addFlash('error', 'Prie mačo prisijungti negalima. Surinktas reikiamas žaidėjų skaičiu.');
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
        $user = $this->getUser();

        if (!$match->getPlayers()->contains($user)) {
            $this->addFlash('error', 'Neįmanoma išeiti iš mačo prie kurio nesate prisijunge!');
            return $this->redirectToRoute('basket_planner_match_show', ['id' => $match->getId()]);
        }

        $match->removePlayer($user);
        $match->decreasePlayersCount();

        if ($match->getPlayersCount() == 0) {
            $match->setActive(false);
        }

        $em = $this->getDoctrine()->getManager();

        $em->flush();

        $this->addFlash('success', 'Sėkmingai išėjote iš mačo');
        return $this->redirectToRoute('basket_planner_match_list');
    }
}