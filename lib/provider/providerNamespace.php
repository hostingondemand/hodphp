<?php
namespace hodphp\lib\provider;
use hodphp\core\Lib;
use hodphp\core\Loader;

class Providernamespace extends Lib
{

    var $namespace;

    function init($namespace)
    {
        $this->namespace = $namespace;
        Loader::loadClass("base" . ucfirst($namespace) . "Provider", "lib/provider/baseprovider");
    }

    function __get($name)
    {
        if ($name == "default") {
            $default = \hodphp\core\core()->config->get("provider." . $this->namespace, "components");
            if ($default) {
                $result = Loader::getSingleton($default, "provider/" . $this->namespace);
                return $result;
            }

            $default = \hodphp\core\core()->config->get("provider." . $this->namespace, "_components");
            if ($default) {
                return Loader::getSingleton($default, "provider/" . $this->namespace);
            }

        }
        return Loader::getSingleton($name, "provider/" . $this->namespace);
    }

    public function __call($name, $arguments)
    {
        return Loader::createInstance($name, "provider/" . $this->namespace);
    }
}


