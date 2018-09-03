<?php

namespace App\Application\SettingsBundle\Twig;

use App\Application\SettingsBundle\Entity\Settings;
use Twig_Extension;
use Twig_SimpleFunction;
use Twig_Environment;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Kernel;


class PathExtension extends \Twig_Extension
{
    protected $doctrine;
protected $container;
protected $tt;
protected $themedir;
    // Retrieve doctrine from the constructor

    public function __construct($doctrine,$container,$tt)
    {
        $this->doctrine = $doctrine;
	$this->container = $container;
	$this->tt = $tt;

	$this->themedir = $this->container->getParameter('kernel.project_dir');
	$this->updatePath();
    }

    public function updatePath(){
        $em = $this->doctrine->getManager();
        $myRepo = $em->getRepository(Settings::class);
	$this->tt->addPath($this->themedir.'/templates/'.$myRepo->find(1)->getVar());
        return true;
    }
 

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('settings_get', array($this, 'getSettings'))
        );
    }

    public function getSettings($opcje)
    {
	$em = $this->doctrine->getManager();
        $myRepo = $em->getRepository(Settings::class);
        return $myRepo->findBy(["name" => $opcje])[0]->getVar();
    }

}