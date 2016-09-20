<?php
namespace lib;
use core\Loader;

class Provider extends \core\Lib{

    var $namespaces= array();
    function __get($name)
    {
        if(!isset($this->namespaces[$name])){
            $namespace=Loader::createInstance("providerNamespace","lib/provider");
            $namespace->init($name);
            $this->namespaces[$name]=$namespace;
        }
        return $this->namespaces[$name];
    }
}
?>