<?php
namespace framework\provider\templateFunction;

//just make a block which will be ignored  by the parser
class Ignore extends \framework\lib\template\AbstractFunction
{

    //this 2 variables do the trick.. this needs content but we dont want it parsed
    var $requireContent = true;
    var $parseContent = false;

    function call($parameters, $data, $content = "", $unparsed = "", $module = false)
    {
        if(isset($parameters[0]) && $parameters[0] && $parameters[0]!="false") {
            return "";
        }
        return $content;

    }

}

