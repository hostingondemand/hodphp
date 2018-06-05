<?php
namespace framework\provider\templateFunction;

class UrlParameter extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return $this->route->parameter($parameters[0],$parameters[1]);
    }
}

