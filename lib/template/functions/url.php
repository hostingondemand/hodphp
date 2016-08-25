<?php
namespace lib\template\functions;

class FuncUrl extends \lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        return $this->route->createRoute($parameters);
    }
}

?>