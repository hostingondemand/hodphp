<?php
namespace framework\provider\templateFunction;

class HtmlDate extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return date("Y-m-d",$parameters[0]);
    }
}

