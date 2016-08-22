<?php
namespace lib\template\functions;

class FuncInclude extends \lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = array(),$module=false)
    {
        $content= $this->template->parseFile($parameters[0],$data);
        return $content;
    }


}

?>