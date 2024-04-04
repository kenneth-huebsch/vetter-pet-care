<?php

namespace Craft;

class Calendar_TwigExtension extends \Twig_Extension
{
    /**
     * @return \Twig_TokenParser[]
     */
    public function getTokenParsers()
    {
        return array(
            new RequireEventEditPermissions_TokenParser(),
        );
    }

}
