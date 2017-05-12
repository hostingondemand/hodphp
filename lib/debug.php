<?php
namespace hodphp\lib;

use hodphp\core\Lib;
use hodphp\core\Loader;

class Debug extends Lib
{

    var $messages = array();
    var $trace = array();
    var $profileStack = array();
    var $profiles = array();

    var $levels = ["debug", "info", "error", "fatal"];

    function getInitArray()
    {
        if ($this->session->_debugMode) {
            return array(
                "messages" => $this->messages,
                "profiles" => $this->removeKeys($this->profiles)
            );
        }

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

    function message($title, $detail, $level)
    {
        $trace = array_slice($this->trace, -5, 5, true);
        $this->messages[] = array("title" => $title, "detail" => $detail, "stackTrace" => $trace, "level" => $level, "levelName" => $this->levels[$level]);
    }

    function fatal($title, $detail)
    {
        $this->message($title, $detail, 3);
    }

    function error($title, $detail)
    {
        $this->message($title, $detail, 2);
    }

    function info($title, $detail)
    {
        $this->message($title, $detail, 1);
    }

    function debug($title, $detail)
    {
        $this->message($title, $detail, 0);
    }

    function handleShutdown()
    {
        $error = error_get_last();
        if ($error['type'] === E_ERROR || $error['type'] == E_PARSE) {
            $this->fatal("PHP:" . $error["message"], $error);
            $this->response->setPartialMode(false);
            Loader::loadAction(array("fatalError", "home"));
            $this->__destruct();
        }
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

    function __destruct()
    {
        $status = $this->config->get("debug.status", "server") ?: false;
        if ($status) {
            $result = "";
            $level = $this->config->get("debug.level", "server") ?: 0;
            $folder = $this->config->get("debug.folder", "server") ?: "data/log/";

            foreach ($this->messages as $message) {
                if ($message["level"] >= $level) {
                    $result .= "--------------" . $message["title"] . "---------------\n";
                    $result .= "Level:" . $message["levelName"] . " \n";
                    $result .= "detail:" . print_r($message["detail"],true)."\n";

                }
            }

            if ($result) {
                $result = "\n==============" . date("d-m-Y H:i:s") . "==============\n" . $result;
                $this->filesystem->mkdir($folder);
                $this->filesystem->append($folder.date("Y-W").".log",$result);
            }

        }
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


