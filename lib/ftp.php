<?php
namespace lib;
use core\Loader;

class Ftp extends \core\Lib{


    function connect($host,$username,$password=""){
        $connection=Loader::createInstance("connection","lib/ftp");
        return    $connection->connect($host,$username,$password);;
    }

}
?>