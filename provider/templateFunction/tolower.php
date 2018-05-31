<?php
namespace framework\provider\templateFunction;

class Tolower extends \framework\lib\template\AbstractFunction
{

    //make a text lowercase
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return strtolower($parameters[0]);
    }
}

