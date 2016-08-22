<?php
namespace lib\template\functions;

class FuncUcfirst extends \lib\template\AbstractFunction
{

    //make the first letter uppercase
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        return ucfirst($parameters[0]);
    }
}

?>