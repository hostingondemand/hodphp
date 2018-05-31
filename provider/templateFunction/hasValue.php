<?php
namespace framework\provider\templateFunction;

class HasValue extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        $haystack=$parameters[0];
        if(is_object($haystack)){
            $haystack=$haystack->getData();
        }
        $needle=$parameters[1];
        return in_array($needle,$haystack);
    }
}

