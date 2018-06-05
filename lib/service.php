<?php
namespace framework\lib;

use framework\core\Lib;
use framework\core\Loader;

//simple wrapper to call services
class Service extends Lib
{
    public function __construct()
    {
        Loader::loadClass("baseService", "lib\\service");
    }

    public function __get($name)
    {
        return Loader::getSingleton($name, "service");
    }

    public function __call($name, $arguments)
    {
        return Loader::createInstance($name, "service");
    }

}

