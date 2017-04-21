<?php
namespace hodphp\lib;

use hodphp\core\Lib;

class Route extends Lib
{
    var $autoRoute = [];

    function createRoute($first = "")
    {

        if (func_num_args() > 1) {
            $first = func_get_args();
        }
        if (!is_array($first)) {
            array_shift($first);
        }

        if ($first[0] == '/') {
            unset($first[0]);
        } else {
            $first = array_merge($this->autoRoute, $first);
        }

        if (is_array($first)) {
            foreach ($first as $key => $val) {
                if (!$val) {
                    $fromRoute = $this->route->get($key);
                    $parameters[$key] = $fromRoute;
                } else {
                    $parameters[$key] = $val;
                }
            }

            if (is_array($this->getRenames()) && isset($renames[$parameters[0]])) {
                $parameters[0] = $renames[$parameters[0]];
            }

            return $this->path->getHttp() . "?route=" . implode("/", $parameters);
        } elseif (!$first) {
            return $this->path->getHttp();
        } else {
            return $this->path->getHttp() . "?route=" . implode("/", func_get_args());
        }
    }

    function getRenames()
    {
        static $renames = false;
        if (!$renames) {
            $renames = $this->config->get("module.rename", "route");
            if (!$renames) {
                $renames = [];
            }
        }

        return $renames;
    }

    function get($key)
    {
        $route = $this->getRoute();
        if (isset($route[$key])) {
            return $route[$key];
        } else {
            return "";
        }
    }

    function getRoute()
    {
        static $route = false;
        if (!$route) {
            if (isset($this->request->get["route"])) {
                $route = explode("/", $this->request->get["route"]);
                $renames = $this->getRenames();
                if (is_array($renames)) {
                    $renames = array_flip($renames);
                    if (isset($renames[$route[0]])) {
                        $route[0] = $renames[$route[0]];
                    }
                }
            }
            if (!$route) {
                $route = [];
            }
        }

        return $route;
    }

    function removeAutoRoute()
    {
        $this->setAutoRoute([]);
    }

    function setAutoRoute($arr)
    {
        $this->autoRoute = $arr;
    }
}

