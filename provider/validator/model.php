<?php
namespace  provider\validator;

use core\Loader;
use lib\validation\BaseValidator;

class model extends BaseValidator{

     var $conditions=array();

     function validate($data){
         $success=true;
         $errors=array();
         $data=$data->toArray();
        foreach($this->conditions as $condition){
            $validator= $condition->validator;
            if(isset($data[$condition->field])){
                $fieldData=
                    (object)array("model"=>$data,"data"=>$data[$condition->field],"options"=>$condition->options);
            }else{
                $fieldData="";
            }

            $subResult=$validator->validate($fieldData);

            if(!$subResult->success){
                $success=false;
                $errors[$condition->field][]=$subResult->errors;
            }
        }
        return $this->result($success,$errors);
     }

     function add($field,$validator,$options=array()){
        $this->conditions[]=(object)array("validator"=>$this->validation->validator($validator),"field"=>$field,"options"=>$options);
         return $this;
     }
}
?>