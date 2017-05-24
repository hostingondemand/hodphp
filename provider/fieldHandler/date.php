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
        $regexVal=preg_replace("/\.[0-9]*/i","",$value);
        if (is_numeric($value)) {
            $this->_data = $value;
            $this->_model->_setInData($this->_inModelName, $value);
        }elseif($dateTime=\DateTime::createFromFormat(\DateTime::W3C,$regexVal)){
            $this->set($dateTime->getTimestamp());
        }elseif($dateTime=\DateTime::createFromFormat("Y-m-d",$value)){
            $this->set($dateTime->getTimestamp());
        }elseif($dateTime=\DateTime::createFromFormat(\DateTime::ATOM,$regexVal)){
            $this->set($dateTime->getTimestamp());
        }else{
            $this->set(0);
        }
    }

}
