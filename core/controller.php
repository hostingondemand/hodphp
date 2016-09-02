<?php
namespace core;
//just a wrapper for controller.. might be useful someday
class Controller extends Base
{
    function __initialize(){
        $this->language->load(Loader::$controller);
        $this->language->load("global");
        $this->language->load("_global");

        $maps=$this->config->get("maps.class","components");
        if(is_array($maps)){
            Loader::$classMaps=$maps;
        }

        $maps=$this->config->get("maps.namespace","components");
        if(is_array($maps)){
            Loader::$classMaps=$maps;
        }

       $this->event->raise("controllerPreLoad",array("controller"=>$this));
        if(method_exists($this,"__onLoad")){
            $this->__onload();
        }
        $this->event->raise("controllerPostLoad",array("controller"=>$this));
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