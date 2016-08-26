<?php
namespace core;
//just a wrapper for controller.. might be useful someday
class Controller extends Base
{
    function __initialize(){
        $this->language->load(Loader::$controller);
        $this->language->load("global");
        $this->language->load("_global");

        if(method_exists($this,"__onLoad")){
            $this->__onload();
        }
    }

    function __authorize(){
        if($this->config->get("deny.".Loader::$module,"access")){
            return false;
        }
        return true;
    }

    function __onAuthorizationFail(){
        throw new \Exception("Authorization failed");
    }


}

?>