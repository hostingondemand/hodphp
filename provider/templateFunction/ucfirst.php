<?php
namespace hodphp\provider\templateFunction;

class Ucfirst extends \hodphp\lib\template\AbstractFunction
{

    //make the first letter uppercase
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return ucfirst($parameters[0]);
    }
}

