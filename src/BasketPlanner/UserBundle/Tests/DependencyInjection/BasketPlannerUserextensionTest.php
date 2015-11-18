<?php

namespace BasketPlanner\UserBundle\Tests\DependencyInjection;

use BasketPlanner\UserBundle\DependencyInjection\BasketPlannerUserExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class BasketPlannerUserExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;
    private $container;

    protected function setUp()
    {
        $this->extension = new BasketPlannerUserExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
    }

    public function testLoad(){

    }


}