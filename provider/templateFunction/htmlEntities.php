<?php
namespace framework\provider\templateFunction;

class HtmlEntities extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = "", $module = false)
    {
        return htmlentities($parameters[0]);
    }
}

