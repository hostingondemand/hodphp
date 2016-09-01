<?php
namespace lib;

class Session extends \core\Lib{
    function __construct()
    {
        session_start();
    }

    function __get($name)
    {
       return $_SESSION[$name];
    }

    function __set($name, $value)
    {
         $_SESSION[$name]=$value;
    }

    function  __destruct()
    {
        session_write_close();
    }
}
?>