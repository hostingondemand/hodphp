<?php
namespace framework\provider\templateFunction;

class DateTime extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        $format = $this->config->get("datetime.format");
        if (!$format) {
            $format = "d-m-Y H:i:s";
        }
        return date($format, $parameters[0]);
    }
}

