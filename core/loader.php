<?php
namespace core;
class Loader
{
    static $instances;
    static $controller;
    static $action;
    static $module;
    static $actionModule;

    static $classMaps = array();
    static $namespaceMaps = array();

    static $setup = false;

    //load an action for a controller.
    static function loadAction($params)
    {
        if (!self::$setup) {
            $setupInstance = new Setup();
            $setupInstance->setup();
            self::$setup = true;
        }

        $paramsFrom = 0;
        $oldController = self::$controller;
        $oldAction = self::$action;
        $oldActionModule = self::$actionModule;
        if (isset($params[$paramsFrom]) && (file_exists("modules/" . $params[$paramsFrom]) || file_exists("project/modules/" . $params[$paramsFrom]))) {
            self::goModule($params[$paramsFrom]);
            self::$actionModule = $params[$paramsFrom];
            $paramsFrom++;
        } else {
            self::goModule("");
            self::$actionModule = "";
        }

        if (!isset($params[$paramsFrom])) {
            $controllerString = "home";
        } else {
            $controllerString = $params[$paramsFrom];
        }

        if ($controller = self::createInstance($controllerString, "controller")) {
            $paramsFrom++;
        } else {
            $controllerString = "home";
            $controller = self::createInstance("home", "controller");
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
            if (!$controller->hasMethod("home")) {
                return false;
            }
            $method = "home";
        }

        $params = array_slice($params, $paramsFrom);
        self::$controller = $controllerString;
        self::$action = $method;

        if ($controller->__authorize()) {
            $controller->__initialize();
            $controller->__preActionCall($method);
            call_user_func_array(Array($controller, $method), $params);
        } else {
            $controller->__onAuthorizationFail();
        }


        self::goBackModule();
        $info = array("module" => self::$actionModule,
            "controller" => self::$controller,
            "action" => self::$action);

        self::$controller = $oldController;
        self::$action = $oldAction;
        self::$actionModule = $oldActionModule;
        return $info;

    }


    //just a method to load a file where a class can be found
    static function loadClass($class, $namespace,$loadHard=false)
    {

        if($loadHard){
            $path = __DIR__ . "/../" . str_replace("\\", "/", $namespace) . "/" . lcfirst($class) . ".php";
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $path=str_replace("/","\\",$path);
            }
            if (file_exists($path)) {
                include_once($path);
                return "\\";
            }
        }else {
            if ($map = self::getClassmapFor($class, $namespace)) {
                $path = __DIR__ . "/../modules/" . $map . "/" . str_replace("\\", "/", $namespace) . "/" . lcfirst($class) . ".php";
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $path=str_replace("/","\\",$path);
                }
                if (file_exists($path)) {
                    include_once($path);
                    return array("prefix" => "\\modules\\" . $map . "\\", "module" => $map);
                }
            }

            if ($map = self::getNamespaceFor($namespace)) {
                $path = __DIR__ . "/../modules/" . $map . "/" . str_replace("\\", "/", $namespace) . "/" . lcfirst($class) . ".php";
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $path=str_replace("/","\\",$path);
                }
                if (file_exists($path)) {
                    include_once($path);
                    return array("prefix" => "\\modules\\" . $map . "\\", "module" => $map);
                }
            }

            $path = __DIR__ . "/../project/modules/" . self::$module . "/" . str_replace("\\", "/", $namespace) . "/" . lcfirst($class) . ".php";
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $path=str_replace("/","\\",$path);
            }
            if (file_exists($path)) {
                include_once($path);
                return "\\project\\modules\\" . self::$module . "\\";
            }

            $path = __DIR__ . "/../modules/" . self::$module . "/" . str_replace("\\", "/", $namespace) . "/" . lcfirst($class) . ".php";
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $path=str_replace("/","\\",$path);
            }
            if (file_exists($path)) {
                include_once($path);
                return "\\modules\\" . self::$module . "\\";
            }


            $path = __DIR__ . "/../project/" . str_replace("\\", "/", $namespace) . "/" . lcfirst($class) . ".php";
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $path=str_replace("/","\\",$path);
            }
            if (file_exists($path)) {
                include_once($path);
                return "\\project\\";
            }


            $path = __DIR__ . "/../" . str_replace("\\", "/", $namespace) . "/" . lcfirst($class) . ".php";
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $path=str_replace("/","\\",$path);
            }
            if (file_exists($path)) {
                include_once($path);
                return "\\";
            }
        }
        return false;
    }

    //load the class if needed. and create an instance of the class
    static function createInstance($class, $namespace = "", $classPrefix = "",$loadHard=false)
    {

        $info=self::getInfo($class,$namespace,$classPrefix,$loadHard);
        if($info){
            return new Proxy($info->type, $info->module);
        }

        return false;

    }

    static function getInfo($class, $namespace = "", $classPrefix = "",$loadHard=false)
    {
        $namespace = str_replace("/", "\\", $namespace);


        if ($loadResult = self::loadClass($class, $namespace,$loadHard)) {
            if (is_array($loadResult)) {
                $prefix = $loadResult["prefix"];
                $module = $loadResult["module"];
            } else {
                $prefix = $loadResult;
                $module = false;
            }

            $className = $class;
            if (is_numeric(substr($className, 0, 1))) {
                $exp = explode(".", $className);
                $className = $exp[1];
            }
            $fullclass = $prefix . $namespace . "\\" . ucfirst($classPrefix) . ucfirst($className);
            return (object)array("type" => $fullclass, "module" => $module);
        }
        return false;
    }


    static function hasMethod($class,$namespace,$method){
        $info=self::getInfo($class,$namespace);
        if($info){
           return method_exists($info->type,$method);
        }
        return false;
    }


    //if an instance of the class is already registered: use this instance otherwise return and register a new instance and register.
    static function getSingleton($class, $namespace = "", $prefix = "",$loadHard=false)
    {
        $namespace = str_replace("/", "\\", $namespace);

        $fullclass = "\\" . $namespace . "\\" . ucfirst($prefix) . ucfirst($class);
        if (!isset(self::$instances[$fullclass])) {
            self::$instances[$fullclass] = self::createInstance($class, $namespace, $prefix,$loadHard);
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

    static $moduleStack = array();

    public static function goModule($name)
    {

        self::$moduleStack[] = Loader::$module;
        self::$module = $name;
    }

    public static function goBackModule()
    {
        self::$module = array_pop(self::$moduleStack);
    }

    public static function getCallerModule()
    {
        return self::$moduleStack[count(self::$moduleStack) - 1];
    }

    static $classStack=array();
    static $currentClass=null;
    public static function registerCall($class){
        self::$classStack[] = self::$currentClass;
        self::$currentClass=$class;
    }
    public static function unregisterCall()
    {
        self::$currentClass = array_pop(self::$classStack);
    }

    public static function getCurrentClass(){
        return self::$currentClass;
    }
}
?>