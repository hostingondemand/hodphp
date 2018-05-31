<?php

//an abstract representation of an handler
namespace framework\lib\template;

use framework\core\Loader;

abstract class BaseModule extends \framework\core\Lib
{
    var $_name;

    //functions

    function callFunction($name, $parameters, $data, $content = "", $unparsed = Array())
    {
        $this->goMyModule();
        $function = $this->getFunction($name);
        $result = $function->call($parameters, $data, $content = "", $unparsed = Array(), $this);
        $this->goBackModule();
        return $result;
    }

    function getFunction($name)
    {
        return Loader::getSingleton($name, "templateModule\\" . $this->_name . "\\functions", "func");
    }

}

