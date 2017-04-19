<?php
namespace hodphp\provider\templateFunction;

use hodphp\core\Loader;

class FuncLanguageCode extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        return $this->language->getCurrentCode();
    }
}

?>