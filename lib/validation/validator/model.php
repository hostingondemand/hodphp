<?php
namespace  lib\validation\validator;

use core\Loader;
use lib\validation\BaseValidator;

class model extends BaseValidator{

     var $conditions;



     function validate($data){
         $success=true;
         $errors=array();
         $data=$data->toArray();
        foreach($this->conditions as $condition){
            $validator= $condition->validator;
            $subResult=$validator->validate($data[$condition->field]);
            if(!$subResult->success){
                $success=false;
                $errors[$condition->field][]=$subResult->errors;
            }
        }

        return $this->result($success,$errors);
     }

     function addCondition($field,$validator){
        $this->conditions[]=(object)array("validator"=>$this->validation->validator($validator),"field"=>$field);
     }
}
?>