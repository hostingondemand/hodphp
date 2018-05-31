<?php
namespace framework\provider\templateFunction;

class CamelCase extends \framework\lib\template\AbstractFunction
{

    function call($parameters, $data, $content = "", $unparsed = "", $module = false)
    {
        //return text in camelcase
        return ucwords($parameters[0], "_");
    }

}

