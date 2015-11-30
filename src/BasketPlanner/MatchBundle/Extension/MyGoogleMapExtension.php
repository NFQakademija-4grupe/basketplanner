<?php

namespace BasketPlanner\MatchBundle\Extension;

use Ivory\GoogleMap\Helper\Extension\ExtensionHelperInterface;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMapBundle\Twig\GoogleMapExtension;

class MyGoogleMapExtension implements ExtensionHelperInterface
{
    /**
     * {@inheritdoc}
     */
    public function renderLibraries(Map $map)
    {
        // Here, we can render additional libraries...
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function renderBefore(Map $map)
    {
        // Here, we can render js code just before the generated one.
        return 'function markerClickEventListener(event) {
                    $("#basket_planner_create_match_court_id").val(this.markerID);
                    $("#basket_planner_create_match_court_address").val(this.markerAddress);
                    $("#basket_planner_create_match_court_latitude").val(this.position.lat());
                    $("#basket_planner_create_match_court_longitude").val(this.position.lng());
                    console.log(event);
                }'.PHP_EOL;
    }

    /**
     * {@inheritdoc}
     */
    public function renderAfter(Map $map)
    {
        // Here, we can render js code just after the generated one.
        return '
                    var mapVariable = '.$map->getJavascriptVariable().';
                    var geocoder;
                    var marker;
                    function initialize() {
                        if (mapVariable.addMarker === true){
                            geocoder = new google.maps.Geocoder();
                            google.maps.event.addListener(mapVariable, "click", function(event) {
                                placeMarker(event.latLng);
                            });
                        }
                    }
                    function placeMarker(location) {
                        if (marker == null){
                            marker = new google.maps.Marker({
                                position: location,
                                map: mapVariable,
                                markerID: 0
                            });
                            google.maps.event.addListener(marker, "click", markerClickEventListener);
                            marker.info = new google.maps.InfoWindow({
                                content: "<br>"
                            });
                            geocodePosition(marker.getPosition());
                            google.maps.event.addListener(marker, "click", function() {
                                marker.info.open(mapVariable, marker);
                            });
                        }else{
                            marker.setPosition(location);
                            geocodePosition(marker.getPosition());
                        }
                    }

                    function geocodePosition(pos) {
                        geocoder.geocode({
                            latLng: pos
                        }, function(responses) {
                            if (responses && responses.length > 0) {
                                var address = responses[0].address_components[1].long_name + " " +
                                              responses[0].address_components[0].long_name + ", " +
                                              responses[0].address_components[2].long_name;
                                updateMarkerAddress(address);
                            } else {
                                updateMarkerAddress("Cannot determine address at this location.");
                            }
                        });
                    }
                    function updateMarkerAddress(str) {
                        var contentText = str + "<br>";
                        marker.markerAddress = str;
                        marker.info.setContent(contentText);
                        new google.maps.event.trigger( marker, "click");
                    }
                    initialize();'.PHP_EOL;
    }
}