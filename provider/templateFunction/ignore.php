<?php
namespace provider\templateFunction;

//just make a block which will be ignored  by the parser
class FuncIgnore extends \lib\template\AbstractFunction
{

    //this 2 variables do the trick.. this needs content but we dont want it parsed
    var $requireContent = true;
    var $parseContent = false;

    function call($parameters, $data, $content = "", $unparsed = "",$module=false)
    {
        return $content;
    }


}

?>