<?php
namespace framework\lib\db;

use framework\core\Lib;

class Pagination extends Lib
{
    var $status=false;
    var $perPage;
    var $page;
    var $pageCount;

    var $query;

    function __construct(){
        $this->page=@$this->request->get["page"]?:1;
    }

    function pagination(){
        return array(
            "count"=>$this->getPageCount(),
            "current"=>$this->page
        );
    }

    function turnOn($perPage){
        $this->perPage=$perPage;
        $this->status=true;
        $this->pageCount=false;
        $this->query="";
    }

    function turnOff(){
        $this->pageCount=false;
        $this->status=false;
        $this->query="";
    }


    function setResultCount($count){
        $this->pageCount=ceil($count/$this->perPage);
    }

    function getPageCount(){
        if($this->pageCount===false){
            $query=$this->db->query($this->query);
            $fetch=$query->fetch();
            $this->pageCount=ceil($fetch["amount"]/$this->perPage);
        }
        return $this->pageCount;
    }

    function getLimit(){
        if($this->status){
            $page=$this->page-1;
            return $page*$this->perPage.",".$this->perPage;
        }
        return false;
    }
}
?>