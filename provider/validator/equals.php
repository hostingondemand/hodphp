<?php
namespace framework\provider\validator;

use framework\lib\validation\BaseValidator;

class Equals extends BaseValidator
{

    function validate($data)
    {
        if ($data->data != $data->model[$data->options["compareTo"]]) {
            return $this->result(false, $this->language->get("notEqual", "_validation"));
        } else {
            return $this->result(true, false);
        }
    }

    function isRequired()
    {
        return false;
    }
}

