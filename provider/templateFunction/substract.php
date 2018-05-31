<?php
namespace framework\provider\templateFunction;

class Substract extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return $parameters[0]-$parameters[1];
    }
}

