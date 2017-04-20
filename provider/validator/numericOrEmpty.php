<?php
namespace hodphp\provider\validator;

use hodphp\lib\validation\BaseValidator;

class NumericOrEmpty extends BaseValidator
{

    function validate($data)
    {
        if (empty($data->data)) {
            return $this->result(true, false);
        }
        if (!is_numeric($data->data)) {
            return $this->result(false, $this->language->get("empty", "_validation"));
        } else {
            return $this->result(true, false);
        }
        function isRequired()
        {
            return false;
        }
    }
}

?>