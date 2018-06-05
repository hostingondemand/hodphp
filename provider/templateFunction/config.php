<?php
namespace framework\provider\templateFunction;

class Config extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        if (@$parameters[1]) {
            return $this->config->get($parameters[0], $parameters[1]);
        } else {
            return $this->config->get($parameters[0]);
        }

    }
}

