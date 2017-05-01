<?php
namespace hodphp\helper;
use hodphp\lib\helper\BaseHelper;

class Str extends BaseHelper
{
    function isValidEmail($email){
        return !(empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL));
    }

    function isGuid($id){
       return preg_match("/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)\})$/i", $id)?true:false;
    }


}

?>
