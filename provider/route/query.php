<?php
namespace hodphp\provider\route;

use hodphp\lib\provider\baseprovider\BaseRouteProvider;

class Query extends BaseRouteProvider
{
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
            $first = array_merge($this->route->autoRoute, $first);
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

            if (is_array($this->route->getRenames()) && isset($renames[@$parameters[0]])) {
                $parameters[0] = $renames[$parameters[0]];
            }

            return $this->path->getHttp() . "?route=" . implode("/", $parameters);
        } elseif (!$first) {
            return $this->path->getHttp();
        } else {
            return $this->path->getHttp() . "?route=" . implode("/", func_get_args());
        }
    }

    function parameter($key, $val)
    {
        $get = $this->request->get;
        $get[$key] = $val;
        $url = $this->path->getHttp();
        $i = 0;
        foreach ($get as $key => $val) {
            if ($i) {
                $url .= "&";
            } else {
                $url .= "?";
            }
            $url .= $key . "=" . urlencode($val);
            $i++;
        }
        return $url;
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
                $renames = $this->route->getRenames();
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
}