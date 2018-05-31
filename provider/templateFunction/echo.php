<?php
namespace framework\provider\templateFunction;

class Echo extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        //just returns the text
        return $parameters[0];
    }
}

