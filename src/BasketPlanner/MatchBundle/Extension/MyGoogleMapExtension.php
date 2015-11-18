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
           console.log(this.markerID);
        }'.PHP_EOL;
    }

    /**
     * {@inheritdoc}
     */
    public function renderAfter(Map $map)
    {
        // Here, we can render js code just after the generated one.
        return;
    }
}