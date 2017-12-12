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
    var $level = -1;
    var $logStatus = -1;
    var $logPrefix = "";

    var $directLogging=-1;


    function setLogPrefix($logPrefix)
    {
        $this->logPrefix = $logPrefix;
    }


    function directLog($message){
        if ($this->directLogging == -1) {
            $this->directLogging = $this->config->get("debug.directLogging", "server") ?: false;
        }

        if($this->directLogging &&  $message["category"]!="file"){
            try {
                if($this->auth && $this->auth->getUserName()){
                    $details.="user: ".$this->auth->getUserName();
                }
                if($this->route){
                    $details.=" route: ".implode("/", $this->route->getRoute());
                }

            }catch(\Exception $exception){

            }
            $folder = $this->config->get("debug.folder", "server") ?: "data/log/";
            $folder .= date("Y-W") . "/";

            $line= date("d-m-Y H:i:s",  $message["time"]) ." (".$details.")  [" . $message["levelName"] . "] " .$message["category"]."->". $message["title"] . "\n";
            if (!empty($message["detail"])) {
                $line .= print_r($message["detail"], true) . "\n";
            }

            $this->filesystem->append($folder . $this->logPrefix . "direct.log", $line);
        }

    }
    function getLevel()
    {

        if ($this->level == -1 || $this->logStatus == -1) {
            $this->level = $this->config->get("debug.level", "server") ?: 0;
            $this->logStatus = $this->config->get("debug.status", "server") ?: false;
        }

        //for the use outside of this class.. There is no log level 5, so 5 means disabled.
        return $this->logStatus ? $this->level : 5;
    }

    function getInitArray()
    {
        if ($this->session->_debugMode) {
            return array(
                "messages" => @$this->messages["all"],
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
            $this->error("PHP:" . @$errstr, array("file" => @$errfile, "line" => @$errline, "errno" => @$errno), "php");
        }
    }

    function message($title, $detail, $level, $category = "general")
    {
        $trace = array_slice($this->trace, -5, 5, true);
        $message = array(
            "category" => $category,
            "title" => $title,
            "detail" => $detail,
            "stackTrace" => $trace,
            "level" => $level,
            "levelName" => $this->levels[$level],
            "time" => time()
        );
        $this->directLog($message);
        $this->messages[$category][] = $message;
        $this->messages["all"][] = $message;
    }

    function fatal($title, $detail, $category = "general")
    {
        $this->message($title, $detail, 3, $category);
    }

    function error($title, $detail, $category = "general")
    {
        $this->message($title, $detail, 2, $category);
    }

    function info($title, $detail, $category = "general")
    {
        $this->message($title, $detail, 1, $category);
    }

    function debug($title, $detail, $category = "general")
    {
        $this->message($title, $detail, 0, $category);
    }

    function handleShutdown()
    {
        $error = error_get_last();
        if ($error['type'] === E_ERROR || $error['type'] == E_PARSE) {
            $this->fatal("PHP:" . $error["message"], $error, "php");
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
        $level = $this->getLevel();

        $user = $this->auth->getUserName();

        if ($this->logStatus) {
            $folder = $this->config->get("debug.folder", "server") ?: "data/log/";
            $folder .= date("Y-W") . "/";
            $this->filesystem->mkdir($folder);
            $route = implode("/", $this->route->getRoute());
            foreach ($this->messages as $categoryName => $category) {
                $result = false;
                foreach ($category as $message) {
                    if ($message["level"] >= $level) {
                        if (!$result) {
                            $result = "============== (user: " . $user . ", route:" . $route . ") ==============\n";
                        }
                        $result .= date("d-m-Y H:i:s", $message["time"]) . " [" . $message["levelName"] . "] " . $message["title"] . "\n";
                        if (!empty($message["detail"])) {
                            $result .= print_r($message["detail"], true) . "\n";
                        }
                    }
                }
                if ($result) {
                    $this->filesystem->append($folder . $this->logPrefix . $categoryName . ".log", $result);
                }
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


