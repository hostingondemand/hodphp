<?php
namespace provider\templateFunction;

class FuncIsClientCacheOn extends \lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        return !$this->session->_debugClientCache?true:false;
    }
}

?>