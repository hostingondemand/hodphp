<?php
namespace framework\lib\model;
use framework\core\Lib;
use framework\core\Loader;

class Modelnamespace extends Lib
{

    var $namespace;

    function init($namespace)
    {
        $this->namespace = $namespace;
    }

    function __get($name)
    {
        return Loader::createInstance($name, $this->namespace);
    }

}


