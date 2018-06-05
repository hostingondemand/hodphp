<?php
namespace framework\provider\templateFunction;

class SectionExists extends \framework\lib\template\AbstractFunction
{
        function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
        {
               $varname="template_sections_".$parameters[0];
               return $this->globals->$varname ?true:false;

        }

}

