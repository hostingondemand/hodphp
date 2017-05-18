<?php
namespace hodphp\provider\templateFunction;

class FuncPagination extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        if(is_object($parameters[0])) {
            $data = $parameters[0]->getData();
            return $this->template->parseFile("components/pagination", $data);
        }
    }
}

