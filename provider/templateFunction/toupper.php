<?php
namespace hodphp\provider\templateFunction;

class Toupper extends \hodphp\lib\template\AbstractFunction
{
    //make a text uppercase
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return strtoupper($parameters[0]);
    }
}

