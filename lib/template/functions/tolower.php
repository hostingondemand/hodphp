<?php
namespace lib\template\functions;

class FuncTolower extends \lib\template\AbstractFunction
{

    //make a text lowercase
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        return strtolower($parameters[0]);
    }
}

?>