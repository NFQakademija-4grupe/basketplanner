<?php

namespace BasketPlanner\UserBundle\Tests\DependencyInjection;

use BasketPlanner\UserBundle\DependencyInjection\BasketPlannerUserExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class BasketPlannerUserExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;
    private $container;
    private $configuration;
    private $loader;
    private $mock;

    protected function setUp()
    {
        $this->extension = new BasketPlannerUserExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);

        $this->mock = $this->getMockBuilder('BasketPlanner\UserBundle\DependencyInjection\BasketPlannerUserExtension')
            ->setMethods(array('processConfiguration'))
            ->getMock();
        $this->mock->expects($this->once())
            ->method('processConfiguration');

        $this->configuration = $this->getMockBuilder('BasketPlanner\UserBundle\DependencyInjection\Configuration');

        $this->loader = $this->getMockBuilder('Loader\YamlFileLoader')
            ->disableOriginalConstructor()
            ->setMethods(array('load'))
            ->getMock();
        $this->loader->expects($this->once())
            ->method('load')->willReturnCallback(function($file){
                return array(
                    $file => true
                );
            });
    }

    /**
     *
     * @param $configs
     * @param $container
     *
     */
    public function testLoad(array $configs, ContainerBuilder $container){
        $configuration = $this->configuration;
        $config = $this->mock->processConfiguration();

        $loader = $this->loader;
        $loader->load('services.yml');

        $this->assertEquals(array('services.yml' => true), $loader->load('services.yml'));
    }


}