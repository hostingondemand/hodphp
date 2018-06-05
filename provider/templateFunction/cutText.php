<?php
namespace framework\provider\templateFunction;

class CutText extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        $text=$parameters[0];
        if(strlen($text)>$parameters[1]){
            $text=substr($text,0,$parameters[1])."...";
        }
        return $text;
    }
}

