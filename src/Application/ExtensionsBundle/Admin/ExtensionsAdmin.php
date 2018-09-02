<?php

namespace App\Application\ExtensionsBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;

class ExtensionsAdmin extends AbstractAdmin {
    protected $baseRoutePattern = 'extensions';
    protected $baseRouteName = 'extensions';

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list']);
        $collection->add('install', 'install/{name}', [], [], [], '', ['http'], ['GET']);
        $collection->add('uninstall', 'uninstall/{name}', [], [], [], '', ['http'], ['GET']);
        $collection->add('upgrade', 'upgrade/{name}', [], [], [], '', ['http'], ['GET']);
        $collection->add('search', 'search/{name}', [], [], [], '', ['http'], ['GET']);
    }
}
