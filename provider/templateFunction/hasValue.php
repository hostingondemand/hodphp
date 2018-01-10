<?php
namespace hodphp\provider\templateFunction;

class FuncHasValue extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        $haystack=$parameters[0]->getData();
        $needle=$parameters[1];
        return in_array($needle,$haystack);
    }
}

