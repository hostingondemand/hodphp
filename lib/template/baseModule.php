<?php

//an abstract representation of an handler
namespace lib\template;
use core\Loader;

abstract class BaseModule extends \core\Base
{
    var $_name;

    //functions
    function getFunction($name){
        return Loader::getSingleton($name, "templateModule\\".$this->_name."\\functions", "func");
    }


    function callFunction($name,$parameters, $data, $content = "", $unparsed = Array()){
        $this->goMyModule();
        $function = $this->getFunction($name);
        $result= $function->call($parameters, $data, $content = "", $unparsed = Array(),$this);
        $this->goBackModule();
        return $result;
    }

}


?>