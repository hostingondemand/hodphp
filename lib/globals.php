<?php
namespace lib;

class Globals extends \core\Lib{

    var $data=array();
    function __get($name)
    {
        if (isset($this->data[$name]) ){
            return $this->data[$name];
        }
        return "";
    }

    function __set($name, $value)
    {
        $this->data[$name]=$value;
    }

}
?>