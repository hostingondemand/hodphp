<?php
namespace hodphp\provider\templateFunction;

class FuncUrlParameter extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return $this->route->parameter($parameters[0],$parameters[1]);
    }
}

