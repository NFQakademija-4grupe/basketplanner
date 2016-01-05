<?php

namespace BasketPlanner\MainBundle\Twig;

class JavascriptContainerExtension extends \Twig_Extension
{

    protected $scripts = [];
    protected $environment;
    protected $assetFunction;

    protected function asset($asset)
    {
        if (empty($this->assetFunction)) {
            $this->assetFunction = $this->environment->getFunction('asset')->getCallable();
        }
        return call_user_func($this->assetFunction, $asset);
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('jsContainerAdd', array($this, 'addScript')),
            new \Twig_SimpleFunction('jsContainerPrint', array($this, 'printScripts')),
        );
    }

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function addScript(array $src)
    {
        foreach ($src as $path)
        {
            $this->scripts[] = $this->asset($path);
        }
    }

    public function printScripts()
    {
        $template = 'BasketPlannerMainBundle:Twig:Javascripts.html.twig';
        return $this->environment->render($template, ['scripts' =>
            $this->scripts]);
    }

    public function getName()
    {
        return 'javascript_container_extension';
    }



}