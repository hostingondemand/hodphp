<?php
namespace framework\provider\validator;

use framework\lib\validation\BaseValidator;

class Numeric extends BaseValidator
{

    function validate($data)
    {
        if (!is_numeric($data->data)) {
            return $this->result(false, $this->language->get("empty", "_validation"));
        } else {
            return $this->result(true, false);
        }
    }

    function isRequired()
    {
        return true;
    }
}

