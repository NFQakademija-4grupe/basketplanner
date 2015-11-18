<?php

namespace BasketPlanner\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ivory\GoogleMap\Overlays\Marker;
use Ivory\GoogleMap\Overlays\InfoWindow;

class TeamController extends Controller
{
    /** @var Ivory\GoogleMapBundle\Model\Map */
    public function indexAction()
    {
        $map = $this->get('ivory_google_map.map');
        $map->setCenter(54.9120769, 23.9515808, true);
        $map->setMapOption('zoom', 10);
        $map->setMapOption('disableDefaultUI', false);
        $javascriptVariable = $map->getJavascriptVariable();

        $marker = new Marker();
        $marker->setPosition(54.9082629, 23.972199, true);
        $marker->setOption('clickable', true);
        $marker->setOption('markerID', '1');

        $infoWindow = new InfoWindow();
        $infoWindow->setContent('<p> Krepsinio aikstele <br/> Kalvariju g. 23 </p>');
        $infoWindow->setAutoClose(true);
        $marker->setInfoWindow($infoWindow);

        $map->addMarker($marker);

        $clickEvent = $this->get('ivory_google_map.event');
        $clickEvent->setInstance($marker->getJavascriptVariable());
        $clickEvent->setEventName('click');
        $clickEvent->setHandle('markerClickEventListener');

        $map->getEventManager()->addEvent($clickEvent);

        return $this->render('BasketPlannerTeamBundle:Team:index.html.twig', array(
            'map' => $map,
            'mapVariable' => $javascriptVariable
        ));
    }

    public function createAction()
    {
        return $this->render('BasketPlannerTeamBundle:Team:create.html.twig');
    }

    public function editAction()
    {
        return $this->render('BasketPlannerTeamBundle:Team:edit.html.twig');
    }

    public function removeAction()
    {
        return $this->render('BasketPlannerTeamBundle:Team:remove.html.twig');
    }
}
