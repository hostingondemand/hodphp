<?php
namespace hodphp\provider\templateFunction;

class Sum extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return $parameters[0]+$parameters[1];
    }
}

