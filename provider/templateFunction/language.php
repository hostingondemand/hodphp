<?php
namespace hodphp\provider\templateFunction;

class Language extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        if (!isset($parameters[1])) {
            $parameters[1] = false;
        }
        $text = $this->language->get($parameters[0], $parameters[1]);

        return $this->template->parse($text, $data);
    }
}

