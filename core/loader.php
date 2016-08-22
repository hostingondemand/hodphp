<?php
namespace core;
class Loader
{
    static $instances;
    static $controller;
    static $action;
    static $module;

    //load an action for a controller.
    static function loadAction($params){

        $paramsFrom=0;
        $oldModule=self::$module;
        $oldController=self::$controller;
        $oldAction=self::$action;

        if(isset($params[$paramsFrom]) && file_exists("modules/".$params[$paramsFrom]))
        {
            self::$module=$params[$paramsFrom];
            $paramsFrom++;
        }

        if(!isset($params[$paramsFrom])){
            $controllerString="home";
        }else{
            $controllerString=$params[$paramsFrom];
        }

        if($controller = self::getSingleton($controllerString,"controller")){
            $paramsFrom++;
        }else{
            $controllerString="home";
            $controller=self::getSingleton("home","controller");
        }

        if(!$controller){
            return false;
        }


        if(!isset($params[$paramsFrom])){
            $method="home";
        }else{
            $method=$params[$paramsFrom];
        }

        if(method_exists($controller,$method)){
            $paramsFrom++;
        }else{
            if(!method_exists($controller,"home")){
                return false;
            }
            $method="home";
        }

        $params=array_slice($params,$paramsFrom);
        self::$controller=$controllerString;
        self::$action=$method;

        if($controller->authorize()){
            call_user_func_array(Array($controller,$method),$params);
        }else{
            $controller->onAuthorizationFail();
        }

        self::$module=$oldModule;
        self::$controller=$oldController;
        self::$action=$oldAction;
        return true;

    }



    //just a method to load a file where a class can be found
    static function loadClass($class, $namespace)
    {


        $path = __DIR__ . "/../project/" . str_replace("\\", "/", $namespace) . "/" . lcfirst($class) . ".php";
        if (file_exists($path)) {
            include_once($path);
            return "\\project\\";
        }


        $path = __DIR__ . "/../modules/".self::$module."/" . str_replace("\\", "/", $namespace) . "/" . lcfirst($class) . ".php";
        if (file_exists($path)) {
            include_once($path);
            return "\\modules\\".self::$module."\\";
        }

        $path = __DIR__ . "/../" . str_replace("\\", "/", $namespace) . "/" . lcfirst($class) . ".php";
        if (file_exists($path)) {
            include_once($path);
            return "\\";
        }
        return false;
    }

    //load the class if needed. and create an instance of the class
    static function createInstance($class, $namespace = "", $classPrefix = "")
    {

        $namespace=str_replace("/","\\",$namespace);


        if ($prefix=self::loadClass($class, $namespace)) {
            $fullclass = $prefix . $namespace . "\\" . ucfirst($classPrefix) . ucfirst($class);
            return new $fullclass();
        }

        return false;

    }


    //if an instance of the class is already registered: use this instance otherwise return and register a new instance and register.
    static function getSingleton($class, $namespace = "", $prefix = "")
    {
        $namespace=str_replace("/","\\",$namespace);

        $fullclass = "\\" . $namespace . "\\" . ucfirst($prefix) . ucfirst($class);
        if (!isset(self::$instances[$fullclass])) {
            self::$instances[$fullclass] = self::createInstance($class, $namespace, $prefix);
        }
        return self::$instances[$fullclass];

    }


}

?>