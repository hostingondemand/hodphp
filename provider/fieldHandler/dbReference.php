<?php
namespace  provider\fieldHandler;
use lib\model\BaseFieldHandler;


class DbReference extends BaseFieldHandler
{
    private $_field;
    private $_toTable;
    private $obj;
    private $loaded;
    private $_toModel;
    private $_fromTable;

    function field($field){
        $this->_field=$field;
        return $this;
    }

    function  toTable($toTable){
        $this->_toTable=$toTable;
        return $this;
    }

    function  fromTable($toTable){
        $this->_fromTable=$toTable;
        return $this;
    }

    function  updateReference(){
        $this->_updateReference=true;
        return $this;
    }

    function  toModel($toTable,$namespace=false){
        $this->_toModel=$toTable;
        $this->_toModelNamespace=$namespace;
        return $this;
    }


    function save(){
        if($this->_updateReference){
            $this->db->query(
                "update `".$this->_fromTable."` set `".$this->_field."`='".$this->obj->id."' where id='".$this->_model->id."'"
            );
        }
    }
    function get($inModel){
        if(!$this->loaded ){
            if($this->_field){
                $field=$this->_field;
                $id=$this->_model->$field;
            }else{
                $id=$inModel;
            }
            $this->obj=$this->db->query("select * from ".$this->_toTable." where id ='".$id."'")->fetchModel($this->_toModel,$this->_toModelNamespace);
            $this->loaded=true;
        }
        return $this->obj;
    }

    function set($obj){
        $this->obj=$obj;
        $this->loaded=true;
    }

}
