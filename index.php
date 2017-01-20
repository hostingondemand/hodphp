<?php
$start=microtime(true);
ob_start();
session_start();
include("app.php");
$app=new App();
$app->run();

echo $start-microtime(true);

?>