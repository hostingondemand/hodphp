<?php
namespace hodphp\provider\templateFunction;

class IsInDebug extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return $this->session->_debugMode ? true : false;
    }
}

