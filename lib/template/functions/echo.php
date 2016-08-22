<?php
namespace lib\template\functions;

class FuncEcho extends \lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        //just returns the text
        return $parameters[0];
    }
}

?>