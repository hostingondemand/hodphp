<?php
namespace lib\db;

use core\Lib;
use core\Loader;
use mysqli;

class Condition extends Lib
{
    var $connector="";
    var $parts=array();

    function eq($left,$right){
        $this->parts[]=$this->parse($left)."=".$this->parse($right);
        $this->connector=" and ";
    }

    function gt($left,$right){

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
        foreach($this->parts as $part){
            if(is_object($part)) {
                $result.=$part->render();
            }else {
                $result .= $part;
            }
        }
    }

    function subCondition($condition){
        $result="";
        if(is_callable($condition)){
           $subCondition=Loader::createInstance("condition","lib/db");
            $condition($subCondition);
        }else{
            $result=$condition;
        }
        return $result;
    }
}

?>