<?php
namespace lib;
use core\Loader;

class Validation extends \core\Lib{

    function  __construct()
    {
        $this->language->load("_validation");
        Loader::LoadClass("BaseValidator","lib/validation");
    }

    function modelValidator($model){
        $validator=$this->createValidator("model");
        $validator->setModel($model);
    }

    function validator($name){
       return Loader::createInstance($name,"lib/validation/validator");
    }



}
?>