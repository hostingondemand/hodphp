<?php
namespace hodphp\lib\model;
use hodphp\core\Lib;
use hodphp\core\Loader;

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

?>
