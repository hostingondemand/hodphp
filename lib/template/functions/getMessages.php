<?php
namespace lib\template\functions;

use core\Loader;

class FuncGetMessages extends \lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        return $this->message->popMessages();
    }
}

?>