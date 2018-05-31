<?php
namespace framework\lib;

class Session extends \framework\core\Lib
{
    function __get($name)
    {
        global $_SESSION;
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        $_SESSION[$name] = false;
        return false;
    }

    function __set($name, $value)
    {
        global $_SESSION;
        $_SESSION[$name] = $value;
    }


    function getAll(){
        global $_SESSION;
        return $_SESSION;
    }

    function simulateFakeSession($data){
        global $_SESSION;
        $_SESSION=$data;
    }

    function __destruct()
    {
        try {
            @session_write_close();
        }catch(\Exception $ex){}
    }
}

