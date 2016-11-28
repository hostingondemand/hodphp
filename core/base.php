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

    public function goMyModule()
    {
        if ($this->__module) {
            $this->goModule($this->__module);
        } else {
            $cls = get_class($this);
            if (substr($cls, 0, 7) == "modules") {
                $exp = explode("\\", $cls);
                $this->goModule($exp[1]);
            } else {
                $this->goModule("");
            }
        }

    }

    public function goModule($name)
    {
        Loader::goModule($name);
    }

    public function goBackModule()
    {
        Loader::goBackModule();
    }


    public function _getType(){
        return get_class($this);
    }

    public function __onClassPostConstruct($data)
    {
        if($this->event) {
            $aspects=$this->annotation->getAnnotationsForClass($data["class"]);
            $this->annotation->runAspect("onClassPostConstruct",$aspects,$data);
            $this->event->raise("classPostConstruct", $data);
        }
    }

    public function __onMethodPreCall($data){
        if($this->event) {
            if (substr($data["method"], 0, 1) != "_") {
                $aspects = $this->annotation->getAnnotationsForMethod($data["class"], $data["method"]);
                $this->annotation->runAspect("onMethodPreCall", $aspects, $data);
            }

            $inModuleAnnotation= $this->annotation->getAnnotationsForMethod($data["class"], $data["method"],"inModule");
            if(!empty($inModuleAnnotation)){
                $annotation=$this->annotation->translate($inModuleAnnotation[0]);
                Loader::goModule($annotation->parameters[0]);
            }

            $this->event->raise("methodPreCall", $data);
        }
    }

    public function __onMethodPostCall($data){
        if($this->event) {
            if(substr($data["method"],0,1)!="_") {
                $aspects = $this->annotation->getAnnotationsForMethod($data["class"], $data["method"]);
                $this->annotation->runAspect("onMethodPostCall", $aspects, $data);


            }

            if($this->annotation->fieldHasAnnotations($data["class"], $data["method"],"inModule")){
                Loader::goBackModule();
            }

            $this->event->raise("methodPostCall", $data);
        }
    }

    public function __onFieldPreGet($data){
        if($this->event) {
            if(substr($data["field"],0,1)!="_") {
                $aspects = $this->annotation->getAnnotationsForField($data["class"], $data["field"]);
                $this->annotation->runAspect("onFieldPreGet", $aspects, $data);
            }
            $this->event->raise("fieldPreGet", $data);
        }
    }

    public function __onFieldPostGet($data){
        if($this->event) {
            if(substr($data["field"],0,1)!="_") {
                $aspects = $this->annotation->getAnnotationsForField($data["class"], $data["field"]);
                $this->annotation->runAspect("onFieldPostGet", $aspects, $data);
            }
            $this->event->raise("fieldPostGet", $data);
        }
    }

    public function __onFieldPreSet($data){
        if($this->event) {
            if(substr($data["field"],0,1)!="_") {
                $aspects = $this->annotation->getAnnotationsForField($data["class"], $data["field"]);
                $this->annotation->runAspect("onFieldPreSet", $aspects, $data);
            }
            $this->event->raise("fieldPreSet", $data);
        }
    }

    public function __onFieldPostSet($data){
        if($this->event) {
            if(substr($data["field"],0,1)!="_") {
                $aspects = $this->annotation->getAnnotationsForField($data["class"], $data["field"]);
                $this->annotation->runAspect("onFieldPostSet", $aspects, $data);
            }
            $this->event->raise("fieldPostSet", $data);
        }
    }


}

?>