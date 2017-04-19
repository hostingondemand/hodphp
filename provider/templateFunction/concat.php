<?php
namespace hodphp\provider\templateFunction;

class FuncConcat extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = "",$module=false)
    {
        //puts multiple strings together
        return implode("", $parameters);
    }
}

?>