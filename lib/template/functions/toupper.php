<?php
namespace lib\template\functions;

class FuncToupper extends \lib\template\AbstractFunction
{
    //make a text uppercase
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        return strtoupper($parameters[0]);
    }
}

?>