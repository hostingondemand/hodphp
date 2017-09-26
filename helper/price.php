<?php
namespace hodphp\helper;

use hodphp\lib\helper\BaseHelper;

class Price extends BaseHelper
{
    function value($price, $decimals = 2)
    {
        $rounded = round($price, $decimals, $this->config->get("currency.rounding", "website") ?: PHP_ROUND_HALF_EVEN);
        return sprintf('%0.' . $decimals . 'f', $rounded);
    }
}