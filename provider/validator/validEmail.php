<?php
namespace hodphp\provider\validator;

use hodphp\core\Loader;
use hodphp\lib\validation\BaseValidator;

class ValidEmail extends BaseValidator{

     function validate($data){
         if(!filter_var($data->data, FILTER_VALIDATE_EMAIL)){
             return $this->result(false,$this->language->get("noValidEmail","_validation"));
         }else{
             return $this->result(true,false);
         }
     }

    function isRequired(){return true;}
}
?>