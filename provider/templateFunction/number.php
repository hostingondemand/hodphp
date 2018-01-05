<?php
namespace hodphp\provider\templateFunction;

class FuncNumber extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        if (is_numeric($parameters[0])) {
            $value = $parameters[0];
        } else {
            $value = 0;
        }

        if(!ctype_digit("".$value) && @$parameters[1]){
            $formatted = $this->helper->price->value($value, @$parameters[1]);
        }elseif(ctype_digit("".$value)||!$value){
            $formatted = $this->helper->price->value($value, 0);
        }else{
            $formatted = $this->helper->price->value($value, $parameters[1]?:2);
        }

        return $formatted;
    }
}

