<?php
namespace hodphp\provider\templateFunction;

class FuncIsInDebug extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return $this->session->_debugMode ? true : false;
    }
}

?>