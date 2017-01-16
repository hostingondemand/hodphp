<?php
namespace  lib;
use core\Lib;
use core\Loader;

class Debug extends Lib
{

    var $errors = array();
    var $trace = array();

    function error($title, $detail)
    {
        $trace=array_slice($this->trace,-5,5,true);
        $this->errors[] = array("title" => $title, "detail" => $detail, "stackTrace"=>$trace);
    }

    function getInitArray()
    {
        return array(
            "errors" => $this->errors
        );
    }

    function handlePHPError($errno, $errstr, $errfile, $errline)
    {
        $reporting = \error_reporting();
        if ($reporting) {
            $this->error("PHP:" . @$errstr, array("file" => @$errfile, "line" => @$errline, "errno" => @$errno));
        }
    }

    function handleShutdown()
    {
        $error = error_get_last();
        if ($error['type'] === E_ERROR || $error['type'] == E_PARSE) {
            $this->error("PHP:" . $error["message"], $error);
            Loader::loadAction(array("fatalError", "home"));
        }
        //die();
    }

    function addToTrace($actionType, $class, $name, $arguments = array())
    {
        $this->trace[] = array("Action type" => $actionType, "Class" => $class, "Name" => $name, "Arguments" => $arguments);
    }

    function removeFromTrace()
    {
        array_pop($this->trace);
    }

    function _debugIn($type, $name, $arguments = array())
    {
    }

    function _debugOut()
    {
    }

}

?>
