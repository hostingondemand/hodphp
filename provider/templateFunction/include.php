<?php
namespace hodphp\provider\templateFunction;

class FuncInclude extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = array(), $module = false)
    {
        $content = $this->template->parseFile($parameters[0], $data);
        return $content;
    }

}

