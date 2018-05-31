<?php
namespace framework\provider\templateFunction;

class KeepGet extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        $input["name"]=$parameters[0];
        $input["value"]=$this->request->get[$parameters[0]];
        return $this->template->parseFile("editorTemplates/hidden", $input);
    }
}