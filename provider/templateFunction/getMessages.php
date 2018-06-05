<?php
namespace framework\provider\templateFunction;

class GetMessages extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return $this->template->dataHandler($this->message->popMessages());
    }
}

