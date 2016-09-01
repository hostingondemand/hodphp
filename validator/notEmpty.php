<?php
namespace  validator;

use core\Loader;
use lib\validation\BaseValidator;

class NotEmpty extends BaseValidator{

     function validate($data){
         if(empty($data)){
             return $this->result(false,$this->language->get("empty","_validation"));
         }else{
             return $this->result(true,false);
         }

     }


}
?>