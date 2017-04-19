<?php
namespace hodphp\lib;
use hodphp\core\Loader;

class Ftp extends \hodphp\core\Lib{


    function connect($host,$username,$password=""){
        $connection=Loader::createInstance("connection","lib/ftp");
        return    $connection->connect($host,$username,$password);;
    }

}
?>