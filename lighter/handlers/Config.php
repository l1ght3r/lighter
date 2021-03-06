<?php
namespace lighter\handlers;


use \Exception;

use lighter\routing\routes\Node as RouteNode;

use lighter\handlers\Debug;


/**
 * the config class. This class permit to acess the config collection and is
 * accessible from the whole application to provide its data.
 *
 * @name Config
 * @package lighter
 * @subpackage models
 * @see lighter\models\DataAccessor
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class Config {
    /**
     * singleton instance
     * @staticvar Config
     */
    private static $instance = NULL;
    /**
     * the configuration tree
     * @var array
     */
    private $configuration = array();
    /**
     * the route tree of the app
     * it's separated from the rest of the configuration to avoid it's overwritted
     * by setSection or setValue.
     * @var RouteNode
     */
    private $routeTree = null;
    private $controllerPaths = array();
    private $templatePaths = array();
    private $roots = array();
    private $preparedRoots = array();


    /**
     * protected constructor
     * as a subclass, the Config class can't restrict the visibility of the parent
     * class.
     */
    protected function __construct() {}


    /**
     * singleton getInstance method
     *
     * @static
     * @return Config
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    public function initApp($mainConfigFile, $appRoot = null) {
        if ($appRoot === null) {
            $appRoot = realpath(dirname($mainConfigFile).'/..');
        }
        define('LIGHTER_APP_PATH', $appRoot);

        $this->roots = array(LIGHTER_APP_PATH, LIGHTER_PATH);

        $this->addControllersSpace('\\lighter\\controllers\\', LIGHTER_PATH);
        $this->addTemplatePath(LIGHTER_PATH.'/lighter/views/templates/');

        require $mainConfigFile;
    }


    /**
     * Set a configuration section. The array of values should not be more than one
     * level deep, unless the value itself is an array. A value should never contain
     * a set of sub-value and thus become a sub-section. If you need to do so, consider
     * creating a new section instead, which is perferable for the usability of the
     * Config object and the readibility of the code.
     *
     * @param string $section
     * @param array $values
     */
    public function setSection($section, array $values) {
        $this->configuration[$section] = $values;
    }


    /**
     * Set a value of the configuration. This value is a part of a section.
     * A value should not be an array, unless the nature of the value is an array.
     *
     * @param string $section
     * @param string $name
     * @param mixed $value
     */
    public function setValue($section, $name, $value) {
        if (isset($this->configuration[$section])) {
            $this->configuration[$section] = array();
        }
        $this->configuration[$section][$name] = $value;
    }


    /**
     * return a whole section of the configuration
     *
     * @param array $section
     */
    public function getSection($section) {
        return $this->configuration[$section];
    }


    /**
     * return a value
     *
     * @param string $section
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getValue($section, $name, $default) {
        if (isset($this->configuration[$section])) {
            if (isset($this->configuration[$section][$name])) {
                return $this->configuration[$section][$name];
            }
        }
        return $default;
    }


    /**
     * set the route tree
     *
     * @param string $regex
     * @param array $route
     */
    public function setRoutes(RouteNode $route) {
        $this->configuration['routes'] = $route;
    }


    /**
     * return the route tree
     *
     * @return RouteNode
     */
    public function getRoutes() {
       return $this->configuration['routes'];
    }


    public function addControllersSpace($space, $root = LIGHTER_APP_PATH) {
        $path = str_replace('\\', '/', $space);
        $this->controllerPaths = array_merge(array($root.$path => $space), $this->controllerPaths);
    }

    public function getControllerFullname($controllerName) {
        foreach ($this->controllerPaths as $path => $package) {
            if (file_exists($path.$controllerName.'.php')) {
                return $package.$controllerName;
            }
        }
        return null;
    }

    public function addTemplatePath($path) {
        array_unshift($this->templatePaths, $path);
    }

    public function getTemplateFullpath($template) {
        foreach ($this->templatePaths as $path) {
            if (file_exists($path.$template.'.php')) {
                return $path.$template.'.php';
            }
        }
    }

    public function completePath($path) {
        //FIXME doesn't seem to be useful
//         $nsRoot = mb_substr($path, 0, mb_strpos('/', $path));
//         if (isset($this->preparedRoots[$nsRoot])) {
//             return $this->preparedRoots[$nsRoot].'/'.$path;
//         } else {
            foreach ($this->roots as $root) {
                $completePath = $root.'/'.$path;
                if (file_exists($completePath)) {
//                     $this->preparedPath[$nsRoot] = $root;
                    return $completePath;
                }
            }
//         }
        return $path;
    }

}


/**
 * The Exception thrown by the Config object.
 *
 * @author michel
 *
 */
class ConfigException extends Exception {}

