<?php

namespace App\Repository;

use Doctrine\ODM\PHPCR\DocumentRepository;
use Symfony\Cmf\Component\Routing\RouteProviderInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route as SymfonyRoute;

class RouteProvider extends DocumentRepository implements RouteProviderInterface
{
    /**
     * This method is used to find routes matching the given URL.
     */
    public function findManyByUrl($url)
    {
        // for simplicity we retrieve one route
        $document = $this->findOneBy([
            'url' => $url,
        ]);

        $pattern = $document->getUrl(); // e.g. "/this/is/a/url"

        $collection = new RouteCollection();

        // create a new Route and set our document as
        // a default (so that we can retrieve it from the request)
        $route = new SymfonyRoute($pattern, [
            'document' => $document,
        ]);

        // add the route to the RouteCollection using
        // a unique ID as the key.
        $collection->add('my_route_'.uniqid(), $route);

        return $collection;
    }

    /**
     * This method is used to generate URLs, e.g. {{ path('foobar') }}.
     */
    public function getRouteByName($name, $params = [])
    {
        $document = $this->findOneBy([
            'name' => $name,
        ]);

        if (!$document) {
            throw new RouteNotFoundException("No route found for name '$name'");
        }

        $route = new SymfonyRoute($document->getUrl(), [
            'document' => $document,
        ]);

        return $route;
    }
}