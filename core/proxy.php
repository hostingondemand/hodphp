<?php
    namespace core;
    class Proxy{
        private $module;
        private $instance;
        private $fullclass;

        function __construct($fullclass,$module)
        {
            $this->fullclass=$fullclass;
            $this->module=$module;
            $this->__setModule();
            $this->instance=new $fullclass();
            $this->instance->__module=$this->module;
            $this->__raise("classPostConstruct",array("class"=>$fullclass),$fullclass);
            $this->__unsetModule();
        }

        function __raise($name,$data){
            $funcName="__on".ucFirst($name);
            if(method_exists($this->instance,$funcName)){
                $this->instance->$funcName($data);
            }
        }

        function __get($name)
        {


            $this->__setModule();
            $this->__raise("fieldPreGet",array("class"=>$this->fullclass,"field"=>$name));
            $result=   $this->instance->$name;
            $this->__raise("fieldPostGet",array("class"=>$this->fullclass,"field"=>$name,"value"=>$result));
            $this->__unsetModule();
            return $result;
        }

        function __set($name, $value)
        {

            $this->__setModule();
            $this->__raise("fieldPreSet",array("class"=>$this->fullclass,"field"=>$name));
            $this->instance->$name=$value;
            $this->__raise("fieldPreGet",array("class"=>$this->fullclass,"field"=>$name));
            $this->__unsetModule();
        }

        function __call($name, $arguments)
        {
            $this->__setModule();
            $this->__raise("methodPreCall",array("class"=>$this->fullclass,"method"=>$name,"arguments"=>$arguments));
            Loader::registerCall($this);
            $result=  call_user_func_array(Array($this->instance, $name), $arguments);
            Loader::unregisterCall($this);
            $this->__raise("methodPostCall",array("class"=>$this->fullclass,"method"=>$name,"arguments"=>$arguments,"value"=>$result));
            $this->__unsetModule();
            return $result;

        }

        function __debugInfo()
        {
            return $this->toArray();

        }

        public function __setModule(){
            if($this->module) {
                Loader::goModule($this->module);
            }
        }

        public function __unsetModule(){
            if($this->module) {
                Loader::goBackModule();
            }
        }

        public function __isset($name)
        {
            return isset($this->instance->$name);
        }

        public function hasMethod($name){
            return method_exists($this->instance,$name);
        }

        public function toArray(){
            $this->__setModule();
            if(method_exists($this->instance,"_debugInfo")){
                $result=   $this->instance->__debugInfo();
            }
            elseif(method_exists($this->instance,"toArray")){
                $result=$this->instance->toArray();
            }else{
                $result=json_decode(json_encode($this->instance),true);
            }
            $this->__unsetModule();
            if(!is_array($result)){
                $result=array();
            }

            return $result;
        }

        function _getType(){
            if(method_exists($this->instance,"_getType")){
                return $this->instance->_getType();
            }else{
                return get_class($this->instance);
            }
        }

    }
?>