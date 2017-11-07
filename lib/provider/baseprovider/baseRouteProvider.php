<?php
namespace hodphp\lib\provider\baseprovider;
use hodphp\core\Lib;

abstract class BaseRouteProvider extends Lib
{
    abstract function createRoute($first = "");
    abstract function parameter($key, $val);
    abstract function get($key);
    abstract function getRoute();
}

