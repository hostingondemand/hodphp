<?php
namespace core;
class Base
{

    private $moduleStack=array();

    public function __get($name)
    {
        //dynamically load libraries
        return Loader::getSingleton($name, "lib");
    }

    public function goModule($name){
        $this->moduleStack[]=Loader::$module;
        Loader::$module=$name;
    }

    public function goBackModule(){
        Loader::$module= array_pop($this->moduleStack);
    }



}

?>