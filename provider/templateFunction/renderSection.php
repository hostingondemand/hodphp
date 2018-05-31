<?php
namespace framework\provider\templateFunction;

class RenderSection extends \framework\lib\template\AbstractFunction
{

        function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
        {
             $varname="template_sections_".$parameters[0];
               return $this->globals->$varname?:"";
        }

}

