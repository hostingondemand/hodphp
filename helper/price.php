<?php
namespace framework\helper;

use framework\lib\helper\BaseHelper;

class Price extends BaseHelper
{
    function value($price, $decimals = 2, $decimalSeparator = false, $thousandsSeparator = false)
    {
        $rounded = round($price, $decimals, $this->config->get("currency.rounding", "website") ?: PHP_ROUND_HALF_EVEN);

        if ( ! $decimalSeparator) {
            $decimalSeparator = $this->config->get("number.decimal_separator", "website") ?: ".";
        }
        if ( ! $thousandsSeparator) {
            $thousandsSeparator = $this->config->get("number.thousands_separator", "website") ?: "";
        }

        return number_format($rounded, $decimals, $decimalSeparator, $thousandsSeparator);
    }
}