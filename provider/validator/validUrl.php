<?php
namespace framework\provider\validator;

use framework\lib\validation\BaseValidator;

class ValidUrl extends BaseValidator
{

    function validate($data)
    {
        if (!filter_var($data->data, FILTER_VALIDATE_URL)) {
            return $this->result(false, $this->language->get("noValidUrl", "_validation"));
        } else {
            return $this->result(true, false);
        }
    }

    function isRequired()
    {
        return true;
    }
}

