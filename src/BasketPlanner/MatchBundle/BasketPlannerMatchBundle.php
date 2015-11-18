<?php

namespace BasketPlanner\MatchBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use BasketPlanner\MatchBundle\DependencyInjection\BasketPlannerMatchExtension;

class BasketPlannerMatchBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new BasketPlannerMatchExtension();
    }
}
