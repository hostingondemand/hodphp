<?php
namespace framework\provider\templateFunction;

class IsInDebug extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return $this->session->_debugMode ? true : false;
    }
}

