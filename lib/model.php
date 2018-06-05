<?php
namespace framework\lib;

use framework\core\Lib;
use framework\core\Loader;

//simple wrapper to call services
class Model extends Lib
{



    public function __construct()
    {
        Loader::loadClass("baseModel", "lib\\model");
        Loader::loadClass("baseFieldHandler", "lib\\model");
    }

    public function __get($name)
    {
        static $namespaceInstances=[];
        $result = Loader::createInstance($name, "model");
        if (!$result) {
            if(!isset($namespaceInstances[$name])) {
                $namespaceInstances[$name] = Loader::createInstance("modelNamespace", "lib\\model");
                if($namespaceInstances[$name]) {
                    $namespaceInstances[$name]->init("model\\" . $name);
                    $namespaceInstances[$name]=$namespaceInstances[$name]->instance;
                }

            }
            $result=$namespaceInstances[$name];
        }
        return $result;
    }

    public function fieldHandler($handler)
    {
        return Loader::createInstance($handler, "provider\\fieldHandler");

    }

}

