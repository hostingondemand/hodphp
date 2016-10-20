<?php
namespace provider\templateFunction;

class FuncEcho extends \lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        //just returns the text
        return $parameters[0];
    }
}

?>