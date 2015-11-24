<?php

namespace BasketPlanner\MatchBundle\Services;

use Ivory\GoogleMap\Overlays\Marker;
use Ivory\GoogleMap\Overlays\InfoWindow;
use Ivory\GoogleMap\Helper\MapHelper;
use BasketPlanner\MatchBundle\Entity\Court;

class GoogleMapLoaderService{

    private $container;

    public function __construct($container){
        $this->container = $container;
    }

    /**
     * @var Ivory\GoogleMapBundle\Model\Map
     * @var boolean $type   Should contain a boolean value to tell which markers to load
     */
    public function loadMarkers($type){

        $map = $this->container->get('ivory_google_map.map');

        $map->setCenter(54.9120769, 23.9515808, true);
        $map->setMapOption('zoom', 10);
        $map->setMapOption('disableDefaultUI', false);

        $em = $this->container->get('doctrine')->getManager()
                   ->getRepository('BasketPlannerMatchBundle:Court')->findByApproved(true);

        foreach($em as $court){
            $marker = new Marker();
            $marker->setPosition($court->getLatitude(), $court->getLongitude(), true);
            $marker->setOption('clickable', true);
            $marker->setOption('markerID', $court->getId());
            $marker->setOption('markerAddress', $court->getAddress());

            $infoWindow = new InfoWindow();
            $infoWindow->setContent($court->getAddress());
            $infoWindow->setAutoClose(true);
            $marker->setInfoWindow($infoWindow);

            $map->addMarker($marker);

            $clickEvent = $this->container->get('ivory_google_map.event');
            $clickEvent->setInstance($marker->getJavascriptVariable());
            $clickEvent->setEventName('click');
            $clickEvent->setHandle('markerClickEventListener');

            $map->getEventManager()->addEvent($clickEvent);
        }

        return $map;
    }
}