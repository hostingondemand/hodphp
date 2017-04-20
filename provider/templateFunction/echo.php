<?php
namespace hodphp\provider\templateFunction;

class FuncEcho extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        //just returns the text
        return $parameters[0];
    }
}

?>