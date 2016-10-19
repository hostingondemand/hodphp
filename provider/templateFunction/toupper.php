<?php
namespace provider\templateFunction;

class FuncToupper extends \lib\template\AbstractFunction
{
    //make a text uppercase
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        return strtoupper($parameters[0]);
    }
}

?>