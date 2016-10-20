<?php
namespace  lib;
use core\Lib;
use core\Loader;

//simple wrapper to call services
class Enum extends Lib{

    public function __construct()
    {
        Loader::loadClass("baseEnum","lib\\enum");;
    }

    public function __get($name)
    {
        $result= Loader::getSingleton($name, "enum");
        return $result;
    }


}
?>