<?php
namespace hodphp\provider\templateFunction;

class FuncCurrency extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        $currency = $this->config->get("currency.symbol", "website") ?: "€";

        if (is_numeric($parameters[0])) {
            $value = $parameters[0];
        } else {
            $value = 0;
        }

        $formatted = $this->helper->price->value($value);

        return $currency . " " . $formatted;
    }
}

