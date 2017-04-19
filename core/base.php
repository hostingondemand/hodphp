<?php
namespace hodphp\core;
class Base
{

    var $__turboMode=false;

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
        }
    }

    public function __onMethodPostCall($data){
        if($this->event) {
            if(substr($data["method"],0,1)!="_") {
                $aspects = $this->annotation->getAnnotationsForMethod($data["class"], $data["method"]);
                $this->annotation->runAspect("onMethodPostCall", $aspects, $data);
            }

            if($this->annotation->methodHasAnnotations($data["class"], $data["method"],"inModule")){
                Loader::goBackModule();
            }
        }
    }

    public function __onFieldPreGet($data){
        if($this->event) {
            if(substr($data["field"],0,1)!="_") {
                $aspects = $this->annotation->getAnnotationsForField($data["class"], $data["field"]);
                $this->annotation->runAspect("onFieldPreGet", $aspects, $data);
            }
        }
    }

    public function __onFieldPostGet($data){
        if($this->event) {
            if(substr($data["field"],0,1)!="_") {
                $aspects = $this->annotation->getAnnotationsForField($data["class"], $data["field"]);
                $this->annotation->runAspect("onFieldPostGet", $aspects, $data);
            }
        }
    }

    public function __onFieldPreSet($data){
        if($this->event) {
            if(substr($data["field"],0,1)!="_") {
                $aspects = $this->annotation->getAnnotationsForField($data["class"], $data["field"]);
                $this->annotation->runAspect("onFieldPreSet", $aspects, $data);
            }
        }
    }

    public function __onFieldPostSet($data){
        if($this->event) {
            if(substr($data["field"],0,1)!="_") {
                $aspects = $this->annotation->getAnnotationsForField($data["class"], $data["field"]);
                $this->annotation->runAspect("onFieldPostSet", $aspects, $data);
            }
        }
    }

    public function _debugIn($type,$name,$arguments=array()){
        //didn't use $this->session to avoid problems in the future
        if(@$_SESSION["_debugMode"]) {
            if ($_SESSION["_debugStacktrace"]) {
                $core = core();
                $debug = Loader::getSingleton("debug", "lib");
                //in case things are not properly loaded yet.
                if ($debug) {
                    $debug->addToTrace($type, $this->_getType(), $name, $arguments);
                }
            }

            if ($_SESSION["_debugProfile"]) {
                $core = core();
                $debug = Loader::getSingleton("debug", "lib");
                //in case things are not properly loaded yet.
                if ($debug) {
                    $debug->profileIn($type, $this->_getType(), $name);
                }
            }
        }
    }

    public function _debugOut(){
        //didn't use $this->session to avoid problems in the future
        if(@$_SESSION["_debugMode"]) {
            if ($_SESSION["_debugStacktrace"]) {
                $debug = Loader::getSingleton("debug", "lib");
                //in case things are not properly loaded yet.
                if ($debug) {
                    $debug->removeFromTrace();
                }
            }

            if ($_SESSION["_debugProfile"]) {
                $debug = Loader::getSingleton("debug", "lib");
                //in case things are not properly loaded yet.
                if ($debug) {
                    $debug->profileOut();
                }
            }
        }
    }


    public function hasMethod($name){
        return method_exists($this,$name);
    }




}

?>