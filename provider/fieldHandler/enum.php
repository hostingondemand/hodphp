<?php
namespace provider\fieldHandler;

use lib\model\BaseFieldHandler;


class Enum extends BaseFieldHandler
{
    private $_name;
    private $_data;

    function name($name){
        $this->_name=$name;
        return $this;
    }

    function get($inModel){
        if(!isset($this->_data)){
            $this->set(0);
        }
        return $this->_data;
    }

    function set($value){
        if(is_object($value)){
            $this->_data=$value;
        }
        else if(is_numeric($value)) {
            $this->_data = $this->enum->{$this->_name}->byValue($value);
        }else{
            $this->_data = $this->enum->{$this->_name}->{$value};
        }
        $this->_model->_setInData($this->_inModelName,$this->_data->value);
    }

}
