<?php
namespace  lib\validation\validator;

use core\Loader;
use lib\validation\BaseValidator;

class model extends BaseValidator{

     function validate($data){
         if(empty($data)){
             return $this->language->get("empty","_validation");
         }else{
             return $this->result(true,false);
         }

     }


}
?>