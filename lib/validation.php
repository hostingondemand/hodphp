<?php
namespace hodphp\lib;
use hodphp\core\Loader;

class Validation extends \hodphp\core\Lib{

    function  __construct()
    {
        $this->language->load("_validation");
        Loader::LoadClass("BaseValidator","lib/validation");
    }


    function validator($name){
       return $this->provider->validator->$name();
    }



}
?>