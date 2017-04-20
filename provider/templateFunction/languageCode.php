<?php
namespace hodphp\provider\templateFunction;

class FuncLanguageCode extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return $this->language->getCurrentCode();
    }
}

?>