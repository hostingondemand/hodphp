<?php
ob_start();
include("app.php");
$app=new App();
$app->run();
?>