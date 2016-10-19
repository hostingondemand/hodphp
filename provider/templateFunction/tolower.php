<?php
namespace provider\templateFunction;

class FuncTolower extends \lib\template\AbstractFunction
{

    //make a text lowercase
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        return strtolower($parameters[0]);
    }
}

?>