<?php
namespace lib\template\functions;

class FuncConcat extends \lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = "",$module=false)
    {
        //puts multiple strings together
        return implode("", $parameters);
    }
}

?>