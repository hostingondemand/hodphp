<?php
namespace hodphp\provider\templateFunction;

class FuncHtmlEntities extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = "", $module = false)
    {
        return htmlentities($parameters[0]);
    }
}

