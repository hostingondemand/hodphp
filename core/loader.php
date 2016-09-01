<?php
namespace core;
class Loader
{
    static $instances;
    static $controller;
    static $action;
    static $module;

    static $classMaps = array();
    static $namespaceMaps = array();

    //load an action for a controller.
    static function loadAction($params)
    {

        $paramsFrom = 0;
        $oldModule = self::$module;
        $oldController = self::$controller;
        $oldAction = self::$action;

        if (isset($params[$paramsFrom]) && file_exists("modules/" . $params[$paramsFrom])) {
            self::$module = $params[$paramsFrom];
            $paramsFrom++;
        }

        if (!isset($params[$paramsFrom])) {
            $controllerString = "home";
        } else {
            $controllerString = $params[$paramsFrom];
        }

        if ($controller = self::getSingleton($controllerString, "controller")) {
            $paramsFrom++;
        } else {
            $controllerString = "home";
            $controller = self::getSingleton("home", "controller");
        }

        if (!$controller) {
            return false;
        }


        if (!isset($params[$paramsFrom])) {
            $method = "home";
        } else {
            $method = $params[$paramsFrom];
        }

        if ($controller->hasMethod($method)) {
            $paramsFrom++;
        } else {
            if ($controller->hasMethod("home")) {
                return false;
            }
            $method = "home";
        }

        $params = array_slice($params, $paramsFrom);
        self::$controller = $controllerString;
        self::$action = $method;

        if ($controller->__authorize()) {
            $controller->__initialize();
            call_user_func_array(Array($controller, $method), $params);
        } else {
            $controller->__onAuthorizationFail();
        }

        self::$module = $oldModule;
        self::$controller = $oldController;
        self::$action = $oldAction;
        return true;

    }


    //just a method to load a file where a class can be found
    static function loadClass($class, $namespace)
    {

        if ($map = self::getClassmapFor($class, $namespace)) {
            $path = __DIR__ . "/../project/modules/" . $map. "/" . str_replace("\\", "/", $namespace) . "/" . lcfirst($class) . ".php";
            if (file_exists($path)) {
                include_once($path);
                return array("prefix"=>"\\project\\modules\\" . $map . "\\","module"=>$map);
            }
        }

        if ($map = self::getNamespaceFor($namespace)) {
            $path = __DIR__ . "/../project/modules/" . $map. "/" . str_replace("\\", "/", $namespace) . "/" . lcfirst($class) . ".php";
            if (file_exists($path)) {
                include_once($path);
                return array("prefix"=>"\\project\\modules\\" . $map . "\\","module"=>$map);
            }
        }

        $path = __DIR__ . "/../project/modules/" . self::$module . "/" . str_replace("\\", "/", $namespace) . "/" . lcfirst($class) . ".php";
        if (file_exists($path)) {
            include_once($path);
            return "\\project\\modules\\" . self::$module . "\\";
        }

        $path = __DIR__ . "/../modules/" . self::$module . "/" . str_replace("\\", "/", $namespace) . "/" . lcfirst($class) . ".php";
        if (file_exists($path)) {
            include_once($path);
            return "\\modules\\" . self::$module . "\\";
        }


        $path = __DIR__ . "/../project/" . str_replace("\\", "/", $namespace) . "/" . lcfirst($class) . ".php";
        if (file_exists($path)) {
            include_once($path);
            return "\\project\\";
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

        $namespace = str_replace("/", "\\", $namespace);


        if ($loadResult = self::loadClass($class, $namespace)) {
            if(is_array($loadResult)){
                $prefix=$loadResult["prefix"];
                $module=$loadResult["module"];
            }else{
                $prefix=$loadResult;
                $module=false;
            }

            $className=$class;
            if(is_numeric(substr($className,0,1))){
                $exp=explode(".",$className);
                $className=$exp[1];
            }
            $fullclass = $prefix . $namespace . "\\" . ucfirst($classPrefix) . ucfirst($className);
            $instance = new $fullclass();
            return new Proxy($instance,$module);
        }

        return false;

    }


    //if an instance of the class is already registered: use this instance otherwise return and register a new instance and register.
    static function getSingleton($class, $namespace = "", $prefix = "")
    {
        $namespace = str_replace("/", "\\", $namespace);

        $fullclass = "\\" . $namespace . "\\" . ucfirst($prefix) . ucfirst($class);
        if (!isset(self::$instances[$fullclass])) {
            self::$instances[$fullclass] = self::createInstance($class, $namespace, $prefix);
        }
        return self::$instances[$fullclass];

    }


    private static function getClassmapFor($class, $namespace)
    {
        if (isset(self::$classMaps[str_replace("\\", "/", $namespace) . "/" . lcfirst($class)])) {
            return self::$classMaps[str_replace("\\", "/", $namespace) . "/" . lcfirst($class)];
        } elseif (isset(self::$classMaps[str_replace("/", "\\", $namespace) . "\\" . lcfirst($class)])) {
            return self::$classMaps[str_replace("/", "\\", $namespace) . "\\" . lcfirst($class)];
        }
        return false;
    }

    private static function getNamespaceFor($namespace)
    {

        if (isset(self::$classMaps[str_replace("\\", "/", $namespace)])) {
            return self::$classMaps[str_replace("\\", "/", $namespace)];
        } elseif (isset(self::$classMaps[str_replace("/", "\\", $namespace)])) {
            return self::$classMaps[str_replace("/", "\\", $namespace)];
        }

        return false;
    }


}

?>