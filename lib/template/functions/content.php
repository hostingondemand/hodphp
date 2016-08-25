<?php
namespace lib\template\functions;

class funcContent extends \lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        $parameters=array_merge(array("_files","content"),$parameters);
        return $this->route->createRoute($parameters);
    }
}

?>