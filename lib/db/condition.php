<?php
namespace lib\db;

use core\Lib;
use core\Loader;
use mysqli;

class Condition extends Lib
{
    var $connector="";
    var $subConnector="";
    var $parts=array();

    function eq($left,$right){
        $this->parts[]=$this->connector." ".$this->parse($left)."=".$this->parse($right);
        $this->connector=" and ";
        return $this;
    }

    //because or is reserved in php
    function bOr(){
        $this->connector=" or ";
        return $this;
    }

    function lteq($left,$right){
        $this->parts[]=$this->connector." ".$this->parse($left)."<=".$this->parse($right);
        $this->connector=" and ";
        return $this;
    }

    function gteq($left,$right){
        $this->parts[]=$this->connector." ".$this->parse($left).">=".$this->parse($right);
        $this->connector=" and ";
        return $this;
    }

    function lt($left,$right){
        $this->parts[]=$this->connector." ".$this->parse($left)."<".$this->parse($right);
        $this->connector=" and ";
        return $this;
    }

    function gt($left,$right){
        $this->parts[]=$this->connector." ".$this->parse($left).">".$this->parse($right);
        $this->connector=" and ";
        return $this;
    }


    function parse($text){
        if(substr($text,0,1)=="'" || substr($text,0,1)=='"'){
            $text=substr($text,1);
            if(substr($text,-1)=="'"||substr($text,-1)=='"'){
                $text=substr($text,0,-1);
            }
            $text=$this->db->escape($text);
            $text="'".$text."'";
        }

        return $text;
    }

    function render(){
        $result="";
        $i=0;
        foreach($this->parts as $part){
            if(is_object($part)) {
                if($i>0) {
                    $result.=" and ";
                }
                    $result .= " (" . $part->render() . ") ";

            }else {
                $result .= $part;
            }
            $i++;
        }
        return $result;
    }

    function sub($condition){
        $result="";
        if(is_callable($condition)){
           $subCondition=$this->db->condition();
           $condition($subCondition);
           $result=$subCondition;
        }else{
            $result=$condition;
        }
        $this->parts[]=$result;
        return $this;
    }
}

?>