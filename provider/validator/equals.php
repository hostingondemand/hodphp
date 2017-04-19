<?php
namespace hodphp\provider\validator;

use hodphp\core\Loader;
use hodphp\lib\validation\BaseValidator;

class Equals extends BaseValidator{

     function validate($data){
         if($data->data!=$data->model[$data->options["compareTo"]]){
             return $this->result(false,$this->language->get("notEqual","_validation"));
         }else{
             return $this->result(true,false);
         }
     }

    function isRequired(){return false;}
}
?>