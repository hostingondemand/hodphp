<?php
namespace hodphp\lib;

use hodphp\core\Lib;
use hodphp\core\Loader;

class Route extends Lib
{
    var $_provider;
    function __construct(){
        $this->_provider=$this->provider->route->default;
    }

    function createRoute($first = ""){
      return  call_user_func_array(array($this->_provider,"createRoute"),func_get_args());
    }

    function parameter($key, $val)
    {
       return  call_user_func_array(array($this->_provider,"parameter"),func_get_args());
    }

    function get($key)
    {
        return call_user_func_array(array($this->_provider,"get"),func_get_args());
    }

    function getRoute()
    {
        return call_user_func_array(array($this->_provider,"getRoute"),func_get_args());
    }

    var $autoRoute = [];

    function getRenames()
    {
        static $renames = false;
        if (!$renames) {
            $renames = $this->config->get("module.rename", "route");
            if (!$renames) {
                $renames = [];
            }
        }

        return $renames;
    }

    function removeAutoRoute()
    {
        $this->setAutoRoute([]);
    }

    function setAutoRoute($arr)
    {
        $this->autoRoute = $arr;
    }
}

