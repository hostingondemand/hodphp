<?php
namespace hodphp\lib\template;
use hodphp\core\Base;
use hodphp\core\Lib;

//an abstraction layer for functions
abstract class AbstractFunction extends Lib
{
    var $requireContent = false;
    var $parseContent = true;
    var $interpreter;

    function __construct()
    {
        $this->interpreter = \hodphp\core\Loader::getSingleton("interpreter", "lib\\template");
    }

    abstract function call($parameters, $data, $content = "", $unparsed = Array(),$module=false);


}

?>