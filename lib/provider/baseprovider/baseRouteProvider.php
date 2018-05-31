<?php
namespace framework\lib\provider\baseprovider;
use framework\core\Lib;

abstract class BaseRouteProvider extends Lib
{
    abstract function createRoute($first = "");
    abstract function parameter($key, $val);
    abstract function get($key);
    abstract function getRoute();
}

