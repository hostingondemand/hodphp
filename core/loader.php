<?php

namespace hodphp\core;
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
    static $moduleStack = array();

    //just a method to load a file where a class can be found
    static $classStack = array();

    //load the class if needed. and create an instance of the class
    static $currentClass = null;

    static function setup()
    {
        if (!self::$setup) {
            $setupInstance = new Setup();
            $setupInstance->setup();
            self::$setup = true;
        }
    }

    static function loadAction($params)
    {
        self::setup();

        $originalParams = $params;
        $paramsFrom = 0;
        $oldController = self::$controller;
        $oldAction = self::$action;
        $oldActionModule = self::$actionModule;
        if (isset($params[$paramsFrom]) && (file_exists(DIR_FRAMEWORK . "modules/" . $params[$paramsFrom]) || file_exists(DIR_MODULES . $params[$paramsFrom]) || file_exists(DIR_PROJECT . "modules/" . $params[$paramsFrom]))) {
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
            if ($controller->__preActionCall($method, $params, $originalParams)) {
                call_user_func_array(Array($controller, $method), $params);
                $controller->__postActionCall($method, $params, $originalParams);
            }
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

    public static function goModule($name)
    {

        self::$moduleStack[] = Loader::$module;
        self::$module = $name;
    }

    //if an instance of the class is already registered: use this instance otherwise return and register a new instance and register.

    static function createInstance($class, $namespace = "", $classPrefix = "", $loadHard = false)
    {

        $info = self::getInfo($class, $namespace, $classPrefix, $loadHard);
        if ($info) {
            return new Proxy($info->type, $info->module);
        }

        return false;

    }

    static function getInfo($class, $namespace = "", $classPrefix = "", $loadHard = false)
    {
        static $infoCache = array();
        $module = self::$module;
        $infoKey = md5(print_r([$module, $class, $namespace], true));
        $info = false;
        if (!isset($infoCache[$infoKey])) {
            $namespace = str_replace("/", "\\", $namespace);
            if ($loadResult = self::loadClass($class, $namespace, $loadHard)) {
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


                if (strpos("\\" . $namespace, $prefix) !== false) {
                    $fullNamespace = "\\" . $namespace . "\\";

                } else {
                    $fullNamespace = $prefix . $namespace . "\\";
                }

                if ($classPrefix && class_exists($fullNamespace . ucfirst($classPrefix) . ucfirst($className))) {
                    $fullclass = $fullNamespace . ucfirst($classPrefix) . ucfirst($className);
                } elseif (class_exists($fullNamespace . ucfirst($className))) {
                    $fullclass = $fullNamespace . ucfirst($className);
                } elseif (class_exists($fullNamespace . "_" . ucfirst($className))) {
                    $fullclass = $fullNamespace . "_" . ucfirst($className);
                }else{
                    //fallback to avoid fatals
                    //todo:good error logging on this to find better solutions
                    $fullclass="\\hodphp\\core\\Base";
                }


                $info = (object)array("type" => $fullclass, "module" => $module);
            }
            $infoCache[$infoKey] = $info;
        } else {
            return $infoCache[$infoKey];
        }
        return $info;
    }

    static function loadClass($class, $namespace, $loadHard = false)
    {
        $namespaceFs = str_replace("\\", "/", $namespace);
        if ($loadHard) {
            $exp = explode("/", $namespaceFs);
            if ($exp[0] == "project") {
                $path = DIR_PROJECT;
                unset($exp[0]);
                $namespacePath = implode("/", $exp);
            } elseif ($exp[0] == "modules") {
                $path = DIR_MODULES;
                unset($exp[0]);
                $namespacePath = implode("/", $exp);
            } else {
                $path = DIR_FRAMEWORK;
                $namespacePath = DIR_FRAMEWORK . $namespaceFs;
            }

            $path .= $namespacePath . "/" . lcfirst($class) . ".php";
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $path = str_replace("/", "\\", $path);
            }
            if (file_exists($path)) {
                include_once($path);
                return "\\";
            }
        } else {
            if ($map = self::getClassmapFor($class, $namespace)) {
                $path = DIR_MODULES . $map . "/" . $namespaceFs . "/" . lcfirst($class) . ".php";
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $path = str_replace("/", "\\", $path);
                }
                if (file_exists($path)) {
                    include_once($path);
                    return array("prefix" => "\\modules\\" . $map . "\\", "module" => $map);
                }
            }

            if ($map = self::getNamespaceFor($namespace)) {
                $path = DIR_MODULES . $map . "/" . $namespaceFs . "/" . lcfirst($class) . ".php";
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $path = str_replace("/", "\\", $path);
                }
                if (file_exists($path)) {
                    include_once($path);
                    return array("prefix" => "\\modules\\" . $map . "\\", "module" => $map);
                }
            }

            $expNamespace = explode("/", $namespaceFs);

            $path = DIR_PROJECT . "modules/" . self::$module . "/" . $namespaceFs . "/" . lcfirst($class) . ".php";
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $path = str_replace("/", "\\", $path);
            }
            if (file_exists($path)) {
                include_once($path);
                return "\\project\\modules\\" . self::$module . "\\";
            }

            if (@$expNamespace[0] == "modules") {
                $path = DIR_MODULES;
                unset($expNamespace[0]);
                $path .= implode("/", $expNamespace) . "/" . lcfirst($class) . ".php";
            } else {
                $path = DIR_MODULES . self::$module . "/" . $namespaceFs . "/" . lcfirst($class) . ".php";
            }

            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $path = str_replace("/", "\\", $path);
            }
            if (file_exists($path)) {
                include_once($path);

                if (strpos($path, 'developer') !== false) {
                    return "\\hodphp\\modules\\" . self::$module . "\\";
                } else {
                    if (substr($namespace, 0, 7) == "modules") {

                        return array("prefix" => "\\", "module" => $expNamespace[1]);
                    }
                    return "\\modules\\" . self::$module . "\\";
                }
            }

            if (@$expNamespace[0] == "modules") {
                $path = DIR_FRAMEWORK . "/modules/";
                unset($expNamespace[0]);
                $path .= implode("/", $expNamespace) . "/" . lcfirst($class) . ".php";
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $path = str_replace("/", "\\", $path);
                }
                if (file_exists($path)) {
                    include_once($path);
                    return "\\";
                }

            } else {
                $path = DIR_FRAMEWORK . "/modules/" . self::$module . "/" . $namespaceFs . "/" . lcfirst($class) . ".php";

                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $path = str_replace("/", "\\", $path);
                }
                if (file_exists($path)) {
                    include_once($path);
                    return "\\hodphp\\modules\\" . self::$module . "\\";
                }

            }


            $path = DIR_PROJECT . $namespaceFs . "/" . lcfirst($class) . ".php";
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $path = str_replace("/", "\\", $path);
            }
            if (file_exists($path)) {
                include_once($path);
                return "\\project\\";
            }

            if (@$expNamespace[0] == "project") {
                $path = DIR_PROJECT;
                unset($expNamespace[0]);
                $path .= implode("/", $expNamespace) . "/" . lcfirst($class) . ".php";;
            } else {
                $path = DIR_PROJECT . self::$module . "/" . $namespaceFs . "/" . lcfirst($class) . ".php";
            }
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $path = str_replace("/", "\\", $path);
            }
            if (file_exists($path)) {
                include_once($path);
                return "\\";
            }

            $path = DIR_FRAMEWORK . $namespaceFs . "/" . lcfirst($class) . ".php";
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $path = str_replace("/", "\\", $path);
            }
            if (file_exists($path)) {
                include_once($path);
                return "\\hodphp\\";
            }
        }
        return false;
    }

    private static function getClassmapFor($class, $namespace)
    {
        if (isset(self::$classMaps[$namespace . "/" . lcfirst($class)])) {
            return self::$classMaps[$namespace . "/" . lcfirst($class)];
        } elseif (isset(self::$classMaps[str_replace("/", "\\", $namespace) . "\\" . lcfirst($class)])) {
            return self::$classMaps[str_replace("/", "\\", $namespace) . "\\" . lcfirst($class)];
        }
        return false;
    }

    private static function getNamespaceFor($namespace)
    {

        if (isset(self::$classMaps[$namespace])) {
            return self::$classMaps[$namespace];
        } elseif (isset(self::$classMaps[str_replace("/", "\\", $namespace)])) {
            return self::$classMaps[str_replace("/", "\\", $namespace)];
        }

        return false;
    }

    public static function goBackModule()
    {
        self::$module = array_pop(self::$moduleStack);
    }

    static function hasMethod($class, $namespace, $method)
    {
        $info = self::getInfo($class, $namespace);
        if ($info) {
            return method_exists($info->type, $method);
        }
        return false;
    }

    static function getSingleton($class, $namespace = "", $prefix = "", $loadHard = false)
    {
        $namespace = str_replace("/", "\\", $namespace);

        $fullclass = "\\" . $namespace . "\\" . ucfirst($prefix) . ucfirst($class);
        if (!isset(self::$instances[$fullclass])) {
            self::$instances[$fullclass] = self::createInstance($class, $namespace, $prefix, $loadHard);
        }
        return self::$instances[$fullclass];

    }

    public static function getCallerModule()
    {
        return self::$moduleStack[count(self::$moduleStack) - 1];
    }

    public static function registerCall($class)
    {
        self::$classStack[] = self::$currentClass;
        self::$currentClass = $class;
    }

    public static function unregisterCall()
    {
        self::$currentClass = array_pop(self::$classStack);
    }

    public static function getCurrentClass()
    {
        return self::$currentClass;
    }
}

