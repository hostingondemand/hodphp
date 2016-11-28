<?php
namespace provider\templateFunction;

class FunccamelCase extends \lib\template\AbstractFunction
{

    function call($parameters, $data, $content = "", $unparsed = "",$module=false)
    {
        //return text in camelcase
        return ucwords($parameters[0], "_");
    }


}

?>