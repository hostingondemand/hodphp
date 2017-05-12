<?php
namespace hodphp\provider\templateFunction;

class FuncSum extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return $parameters[0]+$parameters[1];
    }
}

