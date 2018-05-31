<?php
namespace framework\lib;

use framework\core\Loader;

class Ftp extends \framework\core\Lib
{

    function connect($host, $username, $password = "")
    {
        $connection = Loader::createInstance("connection", "lib/ftp");
        return $connection->connect($host, $username, $password);;
    }

}

