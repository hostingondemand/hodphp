<?php
namespace core;
class Base
{


    var $__module;

    public function __get($name)
    {
        //dynamically load libraries
        return Loader::getSingleton($name, "lib");
    }

    public function goMyModule(){
        if($this->__module){
            $this->goModule($this->__module);
        }else{
            $cls=get_class($this);
            if(substr($cls,0,7)=="modules"){
                $exp=explode("\\",$cls);
                $this->goModule($exp[1]);
            }else{
                $this->goModule("");
            }
        }

    }

    public function goModule($name){
        Loader::goModule($name);
    }

    public function goBackModule(){
        Loader::goBackModule();
    }



}

?>