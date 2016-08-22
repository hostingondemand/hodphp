<?php
namespace  lib\model;
use core\Base;
use core\Loader;

class ModelNamespace extends Base{

    var $namespace;

    function init($namespace){
        $this->namespace=$namespace;
    }

    function __get($name)
    {
       return   Loader::createInstance($name, $this->namespace);
    }
}
?>
