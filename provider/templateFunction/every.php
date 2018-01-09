<?php

namespace hodphp\provider\templateFunction;

class FuncEvery extends \hodphp\lib\template\AbstractFunction
{
    var $requireContent = true;

    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        static $counter = [];
        $amount = $parameters[0] ?: 2;
        $includeStart = $parameters[1]?:0;
        $key = md5(print_r($parameters, true) . "_" . print_r($content, true));
        if (!isset($counter[$key])) {
            $counter[$key] = 0;
        }
        $result = "";
        if ($includeStart && fmod($counter[$key], $amount) == 0 || !$includeStart && fmod($counter[$key]+1, $amount) == 0 && $counter[$key]) {
            $result = $this->interpreter->interpret($content, $data);
        }

        $counter[$key]++;
        return $result;
    }
}

