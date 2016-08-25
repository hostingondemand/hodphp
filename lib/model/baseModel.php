<?php
namespace  lib\model;
use core\Base;



    abstract class BaseModel extends Base{

        private $_data = array();
        private $_invalidated=false;

        function __construct()
        {
            $vars = get_object_vars($this);
            foreach(get_object_vars($this) as $name=>$value){
                if($name!="_data" && $name!="_invalidated"){
                    unset($this->$name);
                    $this->_data[$name]=$value;
                }

            }
        }

        function __get($name)
        {


            $backtrace = debug_backtrace();
            $class=get_class($this);
            if($backtrace[1]["class"]!=$class && method_exists($this,"get".ucfirst($name))){
                $funcName="get".ucfirst($name);
                return $this->$funcName();
            }
            elseif(isset($this->_data[$name])){
                return $this->_data[$name];
            }else{
                return parent::__get($name);
            }

        }

        function __set($name,$value)
        {

            $backtrace = debug_backtrace();
            $class=get_class($this);
            if($backtrace[1]["class"]!=$class && method_exists($this,"set".ucfirst($name))){
                $funcName="get".ucfirst($name);
                $this->$funcName($value);
            }
            else{
                $this->_data[$name]=$value;
            }
            $this->_invalidated=true;

        }

        function __debugInfo()
        {
            return $this->_data;
        }




        function fromArray($data){
            $this->_data=$data;
            return $this;
        }

        function fromRequest(){
                return $this->fromArray($this->request->request);
        }


        function toArray(){
           $result = array();
            foreach ($this->_data as $key=>$val)
            {

                    if(is_object($this->_data[$key]) && method_exists($this->_data[$key],"toArray")){
                        $result[$key]=$this->_data[$key]->toArray();
                    }else{
                        $result[$key]=$this->_data[$key];
                    }

            }

            return $result;
        }

        function _isInvalidated(){
            return $this->_invalidated;
        }

        function _saved(){
            $this->_invalidated=false;
        }

    }
?>
