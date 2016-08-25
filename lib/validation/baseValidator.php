<?php
namespace  lib\validation;
use core\Base;

abstract class BaseValidator extends Base{
    abstract function validate($data);

    function result($success,$errors){
        $result= Loader::CreateInstance("ValidationResult","lib/validation");
        $result->success=$success;
        $result->errors=$errors;
        return $result;
    }
}
?>