<?php
namespace core;
//just a wrapper for controller.. might be useful someday
class Controller extends Base
{
    /**noAspects*/
    function __initialize(){
        $this->language->load(Loader::$controller);
        $this->language->load("global");
        $this->language->load("_global");

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
        $this->event->raise("authorizationFail");
        throw new \Exception("Authorization failed");
    }

    function __preActionCall($method){
        $annotations = $this->annotation->getAnnotationsForMethod($this->_getType(),$method, "http");
        foreach($annotations as $annotation){
            $annotation = $this->annotation->translate($annotation);
            if (strtolower($annotation->function) != strtolower($_SERVER['REQUEST_METHOD'])) {
                throw new \Exception("This method requires ". $annotation->function);
            }
        }
    }


}

?>