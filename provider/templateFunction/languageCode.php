<?php
namespace provider\templateFunction;

use core\Loader;

class FuncLanguageCode extends \lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        return $this->language->getCurrentCode();
    }
}

?>