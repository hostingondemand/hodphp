<?php
namespace lib\template\functions;

use core\Loader;

class funcContent extends \lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        if(Loader::$module){
            $parameters=array_merge(array(Loader::$module,"_files","content"),$parameters);
        }else{
            $parameters=array_merge(array("_files","content"),$parameters);
        }

        return $this->route->createRoute($parameters);
    }
}

?>