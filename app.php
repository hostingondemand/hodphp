<?php

class App
{

    private $_route;
    private $_prepare;
    private $_config;

    function __construct()
    {
        $this->_prepare = function () {
        };
    }

    function setRoute($route)
    {
        $this->_route = $route;
    }

    function prepare($script)
    {
        $this->_prepare = $script;
    }

    function run()
    {
        $this->IncludeCore();
        $prepare = $this->_prepare;
        $prepare($this);
        $this->setConfig();
        \hodphp\core\Loader::loadAction($this->_route);
    }

    private function includeCore()
    {
        include(__DIR__ . "/core/base.php");
        include(__DIR__ . "/core/setup.php");
        include(__DIR__ . "/core/proxy.php");
        include(__DIR__ . "/core/controller.php");
        include(__DIR__ . "/core/lib.php");
        include(__DIR__ . "/core/loader.php");
    }

    function setConfig($config = array())
    {
        if (!defined("DIR_FRAMEWORK")) {
            define("DIR_FRAMEWORK", @$config["filesystem.framework"] ?: __DIR__ . "/");
            define("DIR_MODULES", @$config["filesystem.modules"] ?: __DIR__ . "/modules/");
            define("DIR_PROJECT", @$config["filesystem.project"] ?: __DIR__ . "/project/");
            define("DIR_DATA", @$config["filesystem.data"] ?: __DIR__ . "/data/");

            //There are 2 different starting points for the framework to run from, either the framework itself or a project.
            //The framework injects a key "framework" into this variable. If its not, assume that the framework is started by the project.
            define("APP_MODE", @$config["app_mode"] ?: "project");
        }
    }
}

