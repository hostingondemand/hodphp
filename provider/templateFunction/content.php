<?php
namespace hodphp\provider\templateFunction;

use hodphp\core\Loader;

class funcContent extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        foreach ($parameters as $key => $parameter) {
            if (is_object($parameter)) {
                $parameters[$key] = $parameter->getData();
            }
        }

        if (is_array($parameters[0]) && isset($parameters[0]["module"])) {
            $module = $parameters[0]["module"];
        } elseif (isset($parameters[1])) {
            $module = $parameters[1];
        } elseif (Loader::$module) {
            $module = Loader::$module;
        } else {
            $module = "";
        }

        if (is_array($parameters[0]) && isset($parameters[0]["path"])) {
            $path = $parameters[0]["path"];
        } elseif (!is_array($parameters[0])) {
            $path = $parameters[0];
        } else {
            $path = "";
        }

        if ($module) {
            $parameters = array_merge(array($module, "_files", "content"), array($path));
        } else {
            $parameters = array_merge(array("_files", "content"), array($path));
        }

        $oldAutoRoute = $this->route->autoRoute;
        $this->route->setAutoRoute(array());
        $route = $this->route->createRoute($parameters);
        $this->route->setAutoRoute($oldAutoRoute);
        return $route;

    }
}

