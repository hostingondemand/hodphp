<?php
namespace provider\templateFunction;

class FuncIsInDebug extends \lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        return $this->session->_debugMode?true:false;
    }
}

?>