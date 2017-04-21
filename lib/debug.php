<?php
namespace hodphp\lib;
use hodphp\core\Lib;
use hodphp\core\Loader;

class Debug extends Lib
{

    var $errors = array();
    var $trace = array();
    var $profileStack = array();
    var $profiles = array();

    function getInitArray()
    {
        return array(
            "errors" => $this->errors,
            "profiles" => $this->removeKeys($this->profiles)
        );
    }

    function removeKeys($input)
    {
        $result = array();
        foreach ($input as $val) {
            $result[] = $val;
        }
        return $result;
    }

    function handlePHPError($errno, $errstr, $errfile, $errline)
    {
        $reporting = \error_reporting();
        if ($reporting) {
            $this->error("PHP:" . @$errstr, array("file" => @$errfile, "line" => @$errline, "errno" => @$errno));
        }
    }

    function error($title, $detail)
    {
        $trace = array_slice($this->trace, -5, 5, true);
        $this->errors[] = array("title" => $title, "detail" => $detail, "stackTrace" => $trace);
    }

    function handleShutdown()
    {
        $error = error_get_last();
        if ($error['type'] === E_ERROR || $error['type'] == E_PARSE) {
            $this->error("PHP:" . $error["message"], $error);
            $this->response->setPartialMode(false);
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

    function profileIn($actionType, $class, $name)
    {
        $this->profileStack[] = array("name" => "(" . $actionType . ")" . $class . "->" . $name,
            "start" => $this->microtime_float()
        );
    }

    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    function profileOut()
    {
        $last = array_pop($this->profileStack);
        if (!isset($this->profiles[$last["name"]])) {
            $this->profiles[$last["name"]] = array("name" => $last["name"], "seconds" => 0, "occurances" => 0);
        }
        $time = $this->microtime_float() - $last["start"];
        $this->profiles[$last["name"]]["occurances"]++;
        $this->profiles[$last["name"]]["seconds"] += $time;

    }

    function _debugIn($type, $name, $arguments = array())
    {
    }

    function _debugOut()
    {
    }

    function _profileIn($type, $name, $arguments = array())
    {
    }

    function _finishProfile()
    {
    }

}


