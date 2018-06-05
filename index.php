<?php
ob_start(); 
session_start();
include_once("app.php");

$app = new App();

$app->prepare(
    function ($app) {
        if (file_exists("project/config/server.php")) {
            $config=include("project/config/server.php");
            $config["app_mode"]="framework";
            $app->setConfig($config);
        }
        $router = \framework\core\Loader::getSingleton("route", "lib");
        $app->setRoute($router->getRoute());
    }
);

$app->run();
