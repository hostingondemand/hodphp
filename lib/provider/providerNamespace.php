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

    function getDefaultName(){
        $default = \hodphp\core\core()->config->get("provider." . $this->namespace, "components");
        if($default){
            return $default;
        }
        $default = \hodphp\core\core()->config->get("provider." . $this->namespace, "_components");
        if($default) {
          return $default;
        }
        return false;
    }

    function __get($name)
    {
        if ($name == "default") {
            $default = $this->getDefaultName($name);
            if(is_array($default)){
                $default=$default[0];
            }
            if ($default) {
                return $this->$default;
            }
        }
        if($name=="defaults"){
            $result=[];
            $default = $this->getDefaultName($name);
            if(!is_array($default)){
                $default=[$default];
            }

            foreach($default as $provider){
                $result[]=$this->$provider;
            }
            return $result;
        }

        return Loader::getSingleton($name, "provider/" . $this->namespace);
    }

    public function __call($name, $arguments)
    {
        return Loader::createInstance($name, "provider/" . $this->namespace);
    }
}


