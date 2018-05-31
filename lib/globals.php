<?php
namespace framework\lib;

class Globals extends \framework\core\Lib
{

    var $data = array();

    function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        return "";
    }

    function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    function getAll(){
        return $this->data;
    }

    function initialize($data){
        $this->data=$data;
    }

}

