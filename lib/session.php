<?php
namespace lib;

class Session extends \core\Lib{
    function __get($name)
    {
        if (isset($_SESSION[$name]) ){
            return $_SESSION[$name];
        }
        return "";
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