<?php
namespace  lib\provider;
use core\Base;
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
       return   Loader::getSingleton($name, "provider/".$this->namespace);
    }

    public function __call($name, $arguments){
        return   Loader::createInstance($name, "provider/".$this->namespace);
    }
}
?>
