<?php
ob_start();
session_start();
include("app.php");

$app = new App();

$app->prepare(
    function ($app) {
        if (file_exists("project/config/server.php")) {
            $app->setConfig(include("project/config/server.php"));
        }
        $router = \hodphp\core\Loader::getSingleton("route", "lib");
        $app->setRoute($router->getRoute());
    }
);

$app->run();
?>