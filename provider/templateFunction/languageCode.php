<?php
namespace framework\provider\templateFunction;

class LanguageCode extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return $this->language->getCurrentCode();
    }
}

