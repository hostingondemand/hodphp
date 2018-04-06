<?php
namespace hodphp\provider\fieldHandler;

use hodphp\lib\model\BaseFieldHandler;

class _Array extends BaseFieldHandler
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
        if (is_array($value)) {
            $this->_data=$value;
        }else{
            $this->_data = [$value];
        }
    }

}
