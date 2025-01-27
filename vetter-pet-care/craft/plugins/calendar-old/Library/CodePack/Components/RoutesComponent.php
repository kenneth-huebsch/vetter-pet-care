<?php

namespace Calendar\Library\CodePack\Components;

use Calendar\Library\CodePack\CodePack;
use Craft\RoutesService;

class RoutesComponent extends AbstractJsonComponent
{
    /**
     * Calls the installation of this component
     *
     * @param string $prefix
     */
    public function install($prefix = null)
    {
        /** @var RoutesService $routeService */
        $routeService = \Craft\craft()->routes;

        $data       = $this->getData();
        $demoFolder = $prefix . "/";

        foreach ($data as $route) {
            if (isset($route->urlParts) && is_array($route->urlParts) && isset($route->template)) {
                $urlParts = $route->urlParts;

                array_walk_recursive($urlParts, function(&$value) {
                    $value = stripslashes($value);
                });

                $urlParts[0] = $demoFolder . $urlParts[0];

                $pattern  = "/(\/?)(.*)/";
                $template = preg_replace($pattern, "$1$demoFolder$2", $route->template, 1);

                $routeService->saveRoute($urlParts, $template);
            }
        }
    }

    /**
     * This is the method that sets all vital properties
     * ::$fileName
     */
    protected function setProperties()
    {
        $this->fileName = "routes.json";
    }
}
