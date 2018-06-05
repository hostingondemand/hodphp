<?php
namespace framework\lib\provider\baseprovider;

use framework\core\Lib;

abstract class BaseCacheProvider extends Lib
{
    abstract function setup();
    abstract function saveEntry($name,$data);
    abstract function loadEntry($name);
    abstract function clear();
}

