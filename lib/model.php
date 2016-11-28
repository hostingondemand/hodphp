<?php
namespace  lib;
use core\Lib;
use core\Loader;

//simple wrapper to call services
class Model extends Lib{

    public function __construct()
    {
        Loader::loadClass("baseModel","lib\\model");
        Loader::loadClass("baseFieldHandler","lib\\model");
    }

    public function __get($name)
    {
        $result= Loader::createInstance($name, "model");
        if(!$result){
            $result=Loader::createInstance("modelNamespace","lib\\model");
            $result->init("model\\".$name);
        }
        return $result;
    }

    public function fieldHandler($handler)
    {
        return Loader::createInstance($handler, "provider\\fieldHandler");

   }


}
?>