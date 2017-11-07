<?php
namespace hodphp\lib;

use hodphp\core\Lib;
use hodphp\core\Loader;

//simple wrapper to call services
class Helper extends Lib
{
    public function __construct()
    {
        Loader::loadClass("baseHelper", "lib\\helper");
    }

    public function __get($name)
    {
        return Loader::getSingleton($name, "helper");
    }

    public function __call($name, $arguments)
    {
        return Loader::createInstance($name, "helper");
    }

}

