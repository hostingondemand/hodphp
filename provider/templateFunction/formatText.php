<?php
namespace framework\provider\templateFunction;

class FormatText extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        //just returns the text
        return nl2br(htmlspecialchars($parameters[0]));
    }
}

