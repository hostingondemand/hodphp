<?php
namespace hodphp\provider\templateFunction;

class FormatText extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        //just returns the text
        return nl2br(htmlspecialchars($parameters[0]));
    }
}

