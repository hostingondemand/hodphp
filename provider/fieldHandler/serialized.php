<?php
namespace hodphp\provider\fieldHandler;

use hodphp\lib\model\BaseFieldHandler;

class Serialized extends BaseFieldHandler
{
    private $_data;
    private $loaded=false;

    function fromAnnotation($parameters, $type, $field)
    {

    }


    function get($inModel)
    {
        if (empty($this->_data)) {
            return null;
        }

        return $this->_data;
    }

    function set($value)
    {
        if (is_string($value)) {
            $data = @unserialize($value);
            if($data!==false) {
                $this->_data = unserialize($value);
                $this->_model->_setInData($this->_inModelName, $value);
            }else{
                $this->_data = $value;
                $this->_model->_setInData($this->_inModelName, serialize($value));
            }
        }else{
            $this->_data = $value;
            $this->_model->_setInData($this->_inModelName, serialize($value));
        }


    }

}
