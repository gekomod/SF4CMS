<?php

namespace App\Loader;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouteProviderInterface;

class ExtraLoader implements LoaderInterface
{
    private $loaded = false;

    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "extra" loader twice');
        }

        $routes = new RouteCollection();

        // prepare a new route
        $path = '/extra/{parameter}';
        $defaults = array(
            '_controller' => 'AcmeDemoBundle:Demo:extra',
        );
        $requirements = array(
            'parameter' => '\d+',
        );
        $route = new Route($path, $defaults, $requirements);

        // add the new route to the route collection:
        $routeName = 'extraRoute';
        $routes->add($routeName, $route);

        $this->loaded = true;

        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'extra' === $type;
    }

    public function getResolver()
    {
        // needed, but can be blank, unless you want to load other resources
        // and if you do, using the Loader base class is easier (see below)
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
        // same as above
    }
}