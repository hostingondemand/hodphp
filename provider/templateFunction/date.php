<?php
namespace framework\provider\templateFunction;

class Date extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        $format = $this->config->get("date.format");
        if (!$format) {
            $format = "d-m-Y";
        }
        return date($format, $parameters[0]);
    }
}

