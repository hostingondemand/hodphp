<?php
namespace hodphp\provider\validator;

use hodphp\lib\validation\BaseValidator;

class ValidUrlOrEmpty extends BaseValidator
{

    function validate($data)
    {
        if (empty($data->data)) {
            return $this->result(true, false);
        }
        if (!filter_var($data->data, FILTER_VALIDATE_URL)) {
            return $this->result(false, $this->language->get("noValidUrl", "_validation"));
        } else {
            return $this->result(true, false);
        }
    }

    function isRequired()
    {
        return false;
    }
}

?>