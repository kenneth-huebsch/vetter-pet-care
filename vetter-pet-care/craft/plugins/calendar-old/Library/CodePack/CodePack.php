<?php

namespace Calendar\Library\CodePack;

use Calendar\Library\CodePack\Components\RoutesComponent;
use Calendar\Library\CodePack\Components\TemplatesFileComponent;
use Calendar\Library\CodePack\Exceptions\CodePackException;
use Calendar\Library\CodePack\Exceptions\FileObject\FileObjectException;
use Calendar\Library\CodePack\Exceptions\Manifest\ManifestNotPresentException;
use Craft\IOHelper;
use Calendar\Library\CodePack\Components\AssetsFileComponent;

class CodePack
{
    const MANIFEST_NAME = 'manifest.json';

    /** @var string */
    private $location;

    /** @var Manifest */
    private $manifest;

    /** @var TemplatesFileComponent */
    private $templates;

    /** @var AssetsFileComponent */
    private $assets;

    /**
     * @param string $prefix
     *
     * @return string
     */
    public static function getCleanPrefix($prefix)
    {
        $prefix = preg_replace("/\/+/", "/", $prefix);
        $prefix = trim($prefix, "/");

        return $prefix;
    }

    /**
     * CodePack constructor.
     *
     * @param string $location
     *
     * @throws CodePackException
     * @throws ManifestNotPresentException
     */
    public function __construct($location)
    {
        if (!IOHelper::folderExists($location)) {
            throw new CodePackException(
                sprintf(
                    "CodePack folder does not exist in '%s'",
                    $location
                )
            );
        }

        $this->location  = $location;
        $this->manifest  = $this->assembleManifest();
        $this->templates = $this->assembleTemplates();
        $this->assets    = $this->assembleAssets();
        $this->routes    = $this->assembleRoutes();
    }

    /**
     * @param string $prefix
     *
     * @throws FileObjectException
     */
    public function install($prefix)
    {
        $prefix = self::getCleanPrefix($prefix);

        $this->templates->install($prefix);
        $this->assets->install($prefix);
        $this->routes->install($prefix);
    }

    /**
     * @return Manifest
     */
    public function getManifest()
    {
        return $this->manifest;
    }

    /**
     * @return TemplatesFileComponent
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @return AssetsFileComponent
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * @return RoutesComponent
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Assembles a Manifest object based on the manifest file
     *
     * @return Manifest
     */
    private function assembleManifest()
    {
        $manifest = new Manifest($this->location . '/' . self::MANIFEST_NAME);

        return $manifest;
    }

    /**
     * Gets a TemplatesComponent object with all installable templates found
     *
     * @return TemplatesFileComponent
     */
    private function assembleTemplates()
    {
        $templates = new TemplatesFileComponent($this->location);

        return $templates;
    }

    /**
     * Gets an AssetsComponent object with all installable assets found
     *
     * @return AssetsFileComponent
     */
    private function assembleAssets()
    {
        $templates = new AssetsFileComponent($this->location);

        return $templates;
    }

    /**
     * Gets a RoutesComponent object with all installable routes
     *
     * @return RoutesComponent
     */
    private function assembleRoutes()
    {
        $routes = new RoutesComponent($this->location);

        return $routes;
    }
}
