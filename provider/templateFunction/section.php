<?php
namespace hodphp\provider\templateFunction;

class FuncSection extends \hodphp\lib\template\AbstractFunction
{
        var $requireContent = true;

        function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
        {
               $varname="template_sections_".$parameters[0];
               $this->globals->$varname =$this->interpreter->interpret($content, $data);
               return "";
        }

}

