<?php
namespace  lib\model\fieldHandler;
use lib\model\BaseFieldHandler;


class DbToMany extends BaseFieldHandler
{
    private $_field;
    private $_toTable;
    private $obj;
    private $loaded;
    private $_toModel;

    function field($field){
        $this->_field=$field;
        return $this;
    }

    function  toTable($toTable){
        $this->_toTable=$toTable;
        return $this;
    }

    function  toModel($toTable,$namespace=false){
        $this->_toModel=$toTable;
        $this->_toModelNamespace=$namespace;
        return $this;
    }
    function get($inModel){
        if(!$this->loaded ){
            $this->obj=$this->db->query("select * from `".$this->_toTable."` where `".$this->_field."` ='".$this->model->id."'")->fetchAllModel($this->_toModel,$this->_toModelNamespace);
            $this->loaded=true;
        }
        return $this->obj;
    }
}
