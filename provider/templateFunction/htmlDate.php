<?php
namespace hodphp\provider\templateFunction;

class HtmlDate extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return date("Y-m-d",$parameters[0]);
    }
}

