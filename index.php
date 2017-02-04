<?php
ob_start();
session_start();
include("app.php");
$app=new App();
$app->run();
?>