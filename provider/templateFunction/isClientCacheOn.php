<?php
namespace hodphp\provider\templateFunction;

class FuncIsClientCacheOn extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return !$this->session->_debugClientCache ? true : false;
    }
}

