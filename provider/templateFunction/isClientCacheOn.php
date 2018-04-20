<?php
namespace hodphp\provider\templateFunction;

class IsClientCacheOn extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return !$this->session->_debugClientCache ? true : false;
    }
}

