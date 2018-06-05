<?php
namespace framework\provider\templateFunction;

class IsClientCacheOn extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return !$this->session->_debugClientCache ? true : false;
    }
}

