<?php
namespace framework\provider\templateFunction;

class Toupper extends \framework\lib\template\AbstractFunction
{
    //make a text uppercase
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return strtoupper($parameters[0]);
    }
}

