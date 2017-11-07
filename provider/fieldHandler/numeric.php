<?php
namespace hodphp\provider\fieldHandler;

use hodphp\lib\model\BaseFieldHandler;

class Numeric extends BaseFieldHandler
{

    var $_data;

    function get($inModel)
    {
        if (empty($this->_data)) {
            return "0";
        }

        return $this->_data;
    }

    function set($value)
    {
        if (is_numeric($value)) {
            $this->_data = $value;
            $this->_model->_setInData($this->_inModelName, $value);
        }else{
            $this->_data = "0";
            $this->_model->_setInData($this->_inModelName, "0");
        }
    }

}
