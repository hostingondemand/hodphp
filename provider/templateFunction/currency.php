<?php
namespace hodphp\provider\templateFunction;

class FuncCurrency extends \hodphp\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        $currency = $this->config->get("currency.symbol", "website") ?: "€";
        $roundingMethod = $this->config->get("currency.rounding", "website") ?: PHP_ROUND_HALF_EVEN;

        if (is_numeric($parameters[0])) {
            $value = $parameters[0];
        } else {
            $value = 0;
        }

        $formatted = sprintf("%0.2f", round($value, 2, $roundingMethod));

        return $currency . " " . $formatted;
    }
}

