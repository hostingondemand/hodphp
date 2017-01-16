<?php
namespace provider\templateFunction;

class FuncIsFieldRequired extends \lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        return $data->isFieldRequired($parameters[0]);
    }
}

?>