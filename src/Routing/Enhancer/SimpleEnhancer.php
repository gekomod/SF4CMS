<?php

namespace App\Routing\Enhancer;

use Symfony\Cmf\Component\Routing\Enhancer\RouteEnhancerInterface;
use Symfony\Component\HttpFoundation\Request;

class SimpleEnhancer implements RouteEnhancerInterface
{
    public function enhance(array $defaults, Request $request)
    {
        // ... customize the $defaults array

        return $defaults;
    }
}