<?php
namespace hodphp\provider\fieldHandler;

use hodphp\lib\model\BaseFieldHandler;

class Date extends BaseFieldHandler
{

    var $_data;

    function get($inModel)
    {
        if (empty($this->_data)) {
            $this->set($this->_model->_getFromData($this->_inModelName)?:0);
        }

        return $this->_data;
    }

    function set($value)
    {
        if (is_numeric($value)) {
            $this->_data = $value;
            $this->_model->_setInData($this->_inModelName, $value);
        }else{
            $split=explode("-",$value);
            $this->set(mktime(0,0,0,$split[1],$split[2],$split[0]));
        }
    }

}
