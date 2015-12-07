<?php

namespace BasketPlanner\MatchBundle\Services;

use BasketPlanner\MatchBundle\Entity\Court;
use Doctrine\ORM\EntityManager;
use Ivory\GoogleMap\Overlays\Marker;
use Ivory\GoogleMap\Overlays\InfoWindow;
use Ivory\GoogleMap\Helper\MapHelper;

class GoogleMapLoaderService{

    private $map;
    private $mapEvent;
    private $entityManager;

    public function __construct($map, $mapEvent, EntityManager $entityManager){
        $this->map = $map;
        $this->mapEvent = $mapEvent;
        $this->entityManager = $entityManager;
    }

    /**
     * load court markers from database
     *
     * @var boolean $type   Should contain a boolean value to tell which markers to load
     *
     * @return array
     */
    public function loadMarkers($type){

        $map = clone $this->map;

        $map->setCenter(54.9120769, 23.9515808, true);
        $map->setMapOption('zoom', 10);
        $map->setMapOption('disableDefaultUI', false);
        $map->setMapOption('addMarker', true);

        $courts = $this->entityManager->getRepository('BasketPlannerMatchBundle:Court')->findByApproved($type);

        foreach($courts as $court){
            $options = array(
                'clickable' => true,
                'markerID' => $court->getId(),
                'markerAddress' => $court->getAddress(),
            );
            $marker = $this->createMarker($court->getLatitude(), $court->getLongitude(), true, $options);

            $infoWindow = new InfoWindow();
            $infoWindow->setContent($court->getAddress());
            $infoWindow->setAutoClose(true);
            $marker->setInfoWindow($infoWindow);

            $map->addMarker($marker);

            $clickEvent = clone $this->mapEvent;
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
     * @var boolean $type   Should contain a boolean value to tell which markers to load
     *
     * @return array
     */
    public function loadMarkerById($id){

        $map = clone $this->map;

        $court = $this->entityManager->getRepository('BasketPlannerMatchBundle:Court')
                                     ->findOneBy(array('id' => $id));

        $map->setCenter($court->getLatitude(), $court->getLongitude(), true);
        $map->setMapOption('zoom', 10);
        $map->setMapOption('disableDefaultUI', false);
        $map->setMapOption('addMarker', false);

        $options = array(
            'clickable' => true,
            'markerID' => $court->getId(),
            'markerAddress' => $court->getAddress(),
        );
        $marker = $this->createMarker($court->getLatitude(), $court->getLongitude(), true, $options);

        $infoWindow = new InfoWindow();
        $infoWindow->setContent($court->getAddress());
        $infoWindow->setAutoClose(true);
        $marker->setInfoWindow($infoWindow);

        $map->addMarker($marker);

        return $map;
    }

    /**
     * create new marker
     *
     * @var float $latitude
     * @var float $longitude
     * @var boolean $noWrap
     * @var array $options
     *
     * @return Marker
     */
    public function createMarker($latitude, $longitude, $noWrap, $options)
    {
        $marker = new Marker();
        $marker->setPosition($latitude, $longitude, $noWrap);
        foreach ($options as $key => $value) {
            $marker->setOption($key, $value);
        }

        return $marker;
    }
}