<?php

namespace App\Application\ExtensionsBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Annotation\RouteCollection;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Packagist\Api\Client;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ExtensionsCRUDController extends CRUDController
{    
    /**
     * @var Composer
     */
    protected $composer;
    protected $pack;

    public function listAction()
    {
        $project_dir = $this->container->getParameter('kernel.project_dir').'/../';

        $process = new Process('export HOME=$HOME:'.$project_dir.' && /usr/local/bin/composer show -v -l --working-dir=/var/www/webpi --format=json');
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $ot = $process->getOutput();
        $dane = json_decode($ot);
        
        return $this->render('@Extensions/admin/custom_view.html.twig',['tab' => $dane->installed]);
    }
    
    public function installAction($name,KernelInterface $kernel)
    {
        
        $project_dir = $this->container->getParameter('kernel.project_dir').'/../';

        $process = new Process('export HOME=$HOME:'.$project_dir.' && /usr/local/bin/composer show -v --working-dir=/var/www/webpi --format=json');
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $ot = $process->getOutput();
        $dane = json_decode($ot);
        
        echo $dane->installed[0]->name;
        return $this->render('@Extensions/admin/install.html.twig', ['name' => $name]);
    }
    
    public function upgradeAction($name,KernelInterface $kernel)
    {
        $rep = str_replace(",","/",$name);
        
        $project_dir = $this->container->getParameter('kernel.project_dir').'/../';

        $process = new Process('export HOME=$HOME:'.$project_dir.' && COMPOSER_CACHE_DIR=/dev/null /usr/local/bin/composer update '.$rep.' --working-dir=/var/www/webpi');
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $ot = $process->getOutput();
        
        return $this->render('@Extensions/admin/upgrade.html.twig', ['name' => $name, 'output' => $ot]);
    }
    
    public function uninstallAction($name)
    {
        return $this->render('@Extensions/admin/install.html.twig', ['name' => $name]);
    }
    
    public function searchAction($name)
    {
        
        $nam = str_replace("-","/",$name);
        
        $client = new \Packagist\Api\Client();
        $results = $client->search($nam);
        var_export($results);
        
        return $this->render('@Extensions/admin/install.html.twig', ['name' => $name]);
    }
    
    public function createAction()
        {
        return $this->render('@Extensions/admin/create.html.twig');
        }
}
