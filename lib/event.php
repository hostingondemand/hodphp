<?php
namespace hodphp\lib;

use hodphp\core\Loader;

class Event extends \hodphp\core\Lib
{

    var $eventListeners = array();
    var $_noCache;

    var $debugLevel; //filter logging to avoid big overhead when logging is turned off.

    function __construct()
    {
        $this->debugLevel = $this->debug->getLevel();
    }

    function raise($name, $data)
    {
        Loader::loadClass("baseListener", "lib\\event");
        $listeners = $this->getListenersForEvent($name);
        foreach ($listeners as $listener) {
            $this->goModule($listener["module"]);
            $result = $listener["listener"]->handle($data);
            if ($result) {
                $data = $result;
            }
            $this->goBackModule();
        }

        if ($this->debugLevel <= 2) {
            $this->debug->info("Raised event:", array("name"=>$name,"data"=>$data),"event");
        }

        return $data;
    }

    function getListenersForEvent($name)
    {
        if (!isset($this->eventListeners[$name]) || $this->_noCache) {
            if ($this->_noCache) {
                $classes = $this->doGetEventListeners($name);
            } else {
                $classes = $this->cache->runCachedProject("event", array("name" => $name), function ($data) {
                    return $this->doGetEventListeners($data["name"]);
                });
            }
            $result = array();
            foreach ($classes as $class) {
                if ($class["module"]) {
                    $this->goModule($class["module"]);
                }
                $result[] = array("module" => $class["module"], "listener" => Loader::getSingleton($class["class"], $class["namespace"], "", true));
                if ($class["module"]) {
                    $this->goBackModule();
                }
            }
            $this->eventListeners[$name] = $result;
        }
        return $this->eventListeners[$name];

    }

    function doGetEventListeners($name)
    {
        $modules = $this->config->get("requirements.modules", "components");
        $classes = array();
        if ($listener = Loader::getSingleton($name, "project\\listener", "", true)) {
            $classes[] = array("module" => false, "class" => $name, "namespace" => "project\\listener");
        }

        if ($listener = Loader::getSingleton($name, "listener", "", true)) {
            $classes[] = array("module" => false, "class" => $name, "namespace" => "listener");
        }

        $dirs = $this->filesystem->getDirs("modules");
        foreach ($dirs as $module) {
            if(in_array($module,$modules)) {
                $this->goModule($module);
                if ($listener = Loader::getSingleton($name, "modules/" . $module . "/listener", "", true)) {
                    $classes[] = array("module" => $module, "class" => $name, "namespace" => "modules/" . $module . "/listener");
                }
                $this->goBackModule();
            }
        }

        return $classes;
    }

    function noCache()
    {
        $this->_noCache = true;
    }
}


