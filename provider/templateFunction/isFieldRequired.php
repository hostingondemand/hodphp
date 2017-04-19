<?php
namespace hodphp\provider\templateFunction;

class FuncIsFieldRequired extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        return $data->isFieldRequired($parameters[0]);
    }
}

?>