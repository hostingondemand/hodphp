<?php
namespace framework\provider\templateFunction;

class IsFieldRequired extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return $data->isFieldRequired($parameters[0]);
    }
}

