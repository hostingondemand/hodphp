<?php
namespace lib\template;
use core\Base;
//an abstraction layer for functions
abstract class AbstractFunction extends Base
{
    var $requireContent = false;
    var $parseContent = true;
    var $interpreter;

    function __construct()
    {
        $this->interpreter = \core\Loader::getSingleton("interpreter", "lib\\template");
    }

    abstract function call($parameters, $data, $content = "", $unparsed = Array(),$module=false);


}

?>