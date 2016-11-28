<?php
namespace provider\templateFunction;

use core\Loader;

class FuncGetMessages extends \lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        return $this->template->dataHandler($this->message->popMessages());
    }
}

?>