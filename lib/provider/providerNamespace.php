<?php
namespace  lib\provider;
use core\Base;
use function core\core;
use core\Lib;
use core\Loader;

class ProviderNamespace extends Lib{

    var $namespace;

    function init($namespace){
        $this->namespace=$namespace;
        Loader::loadClass("base".ucfirst($namespace)."Provider","lib/provider/baseprovider");
    }

    function __get($name)
    {
        if($name=="default"){
            $default=core()->config->get("provider.".$this->namespace,"components");
            if($default){
                $result=Loader::getSingleton($default, "provider/".$this->namespace);
                return   $result;
            }

            $default=core()->config->get("provider.".$this->namespace,"_components");
            if($default){
                return   Loader::getSingleton($default, "provider/".$this->namespace);
            }

        }
       return   Loader::getSingleton($name, "provider/".$this->namespace);
    }

    public function __call($name, $arguments){
        return   Loader::createInstance($name, "provider/".$this->namespace);
    }
}
?>
