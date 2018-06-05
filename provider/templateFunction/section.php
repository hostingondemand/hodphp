<?php
namespace framework\provider\templateFunction;

class Section extends \framework\lib\template\AbstractFunction
{
        var $requireContent = true;

        function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
        {
               $varname="template_sections_".$parameters[0];
               $this->globals->$varname =$this->interpreter->interpret($content, $data);
               return "";
        }

}

