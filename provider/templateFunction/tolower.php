<?php
namespace hodphp\provider\templateFunction;

class FuncTolower extends \hodphp\lib\template\AbstractFunction
{

    //make a text lowercase
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return strtolower($parameters[0]);
    }
}

?>