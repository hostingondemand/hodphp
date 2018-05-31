<?php
namespace framework\provider\templateFunction;

class Concat extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = "", $module = false)
    {
        //puts multiple strings together
        return implode("", $parameters);
    }
}

