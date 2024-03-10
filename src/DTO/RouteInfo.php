<?php

namespace Openapi\ServerGenerator\DTO;

class RouteInfo
{
    public function __construct(
        public ExtractedRouteController $extractedRouteController,
        public RouteConfiguration $routeConfiguration
    ) {
    }

    public static function create(
        ExtractedRouteController $extractedRouteController,
        RouteConfiguration $routeConfiguration
    ): self {
        return new static($extractedRouteController, $routeConfiguration);
    }
}
