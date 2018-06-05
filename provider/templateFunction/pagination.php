<?php
namespace framework\provider\templateFunction;

class Pagination extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        if(is_object($parameters[0])) {
            $data = $parameters[0]->getData();
            return $this->template->parseFile("components/pagination", $data);
        }
    }
}

