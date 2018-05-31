<?php
namespace framework\lib;

use framework\core\Lib;
use framework\core\Loader;

//simple wrapper to call services
class Enum extends Lib
{

    public function __construct()
    {
        Loader::loadClass("baseEnum", "lib\\enum");;
    }

    public function __get($name)
    {
        $result = Loader::getSingleton($name, "enum");
        return $result;
    }

}

