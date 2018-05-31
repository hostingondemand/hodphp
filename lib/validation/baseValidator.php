<?php
namespace framework\lib\validation;

use framework\core\Base;
use framework\core\Loader;

abstract class BaseValidator extends Base
{
    abstract function validate($data);

    function result($success, $errors)
    {
        $result = Loader::CreateInstance("ValidationResult", "lib/validation");
        $result->success = $success;
        $result->errors = $errors;
        return $result;
    }

    function isRequired()
    {
        return false;
    }
}

