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
     * load court markers from database
     *
     * @var Ivory\GoogleMapBundle\Model\Map
     * @var boolean $type   Should contain a boolean value to tell which markers to load
     *
     * @return array
     */
        public function loadMarkers($type){

            $map = $this->container->get('ivory_google_map.map');

            $map->setCenter(54.9120769, 23.9515808, true);
            $map->setMapOption('zoom', 10);
            $map->setMapOption('disableDefaultUI', false);
            $map->setMapOption('addMarker', true);

            $courts = $this->container->get('doctrine')->getManager()
                ->getRepository('BasketPlannerMatchBundle:Court')->findByApproved(true);

            foreach($courts as $court){
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

        /**
         * load court marker by id
         *
         * @var Ivory\GoogleMapBundle\Model\Map
         * @var boolean $type   Should contain a boolean value to tell which markers to load
         *
         * @return array
         */
        public function loadMarkerById($id){

            $map = $this->container->get('ivory_google_map.map');

            $court = $this->container->get('doctrine')
                    ->getManager()
                    ->getRepository('BasketPlannerMatchBundle:Court')
                    ->findOneBy(array('id' => $id));

            $map->setCenter($court->getLatitude(), $court->getLongitude(), true);
            $map->setMapOption('zoom', 10);
            $map->setMapOption('disableDefaultUI', false);
            $map->setMapOption('addMarker', false);

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

            return $map;
        }

    /**
     * @var Ivory\GoogleMapBundle\Model\Map
     * @var boolean $type   Should contain a boolean value
     */
    public function loadAddress($type){

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