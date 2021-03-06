<?php
ob_start();  
session_start();
include_once("app.php");

$app = new App();

$app->prepare(
    function ($app) {
        $cwd=getcwd();
        if (file_exists($cwd."/config/server.php")) {
            $config=include($cwd."/config/server.php");
            $config["app_mode"]="console";
            $app->setConfig($config);
        }
        $console = framework\core\Loader::getSingleton("console", "lib");

        if (!$console->isConsole()) {
            die("This request is not in console mode");
        }

        $route = $console->getRoute();
        unset($route[0]);
        if ($route[1] == 'cron') {
            unset($route[1]);
            $route = array_merge(["_cron"], $route);
        } else {
            $route = array_merge(["developer", "console"], $route);
        }
        $app->setRoute($route);
    }
);

$app->run();
