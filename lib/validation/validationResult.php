<?php
namespace  lib\validation;
use core\Base;

//just a dummy to use for services.. to avoid a lot of refactoring in the future..
// can always be useful for result checks etc in the future.
class ValidationResult extends Base{
    var $success;
    var $errors;
}
?>