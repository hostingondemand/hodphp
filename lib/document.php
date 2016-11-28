<?php
namespace lib;

class Document
{

    var $scripts=array();
    var $stylesheets=array();
    var $vars=array();


    function addScript($script,$priority=0){
        $this->scripts[$priority][]=$script;
    }

    function addStylesheet($stylesheet,$priority=0){
        $this->stylesheets[$priority][]=$stylesheet;
    }

    function getScripts(){
        $result=array();
        krsort($this->scripts);
        reset($this->scripts);
        foreach($this->scripts as $scripts){
            $result=array_merge($result,$scripts);
        }
        return $result;
    }

    function getStylesheets(){
        $result=array();
        krsort($this->stylesheets);
        foreach($this->stylesheets as $stylesheets){
            $result=array_merge($result,$stylesheets);
        }
        return $result;
    }

    function addVar($key,$value){
        $this->vars[$key]=$value;
    }

    function  getVars()
    {
        $result=array_map(function($var){
            return json_encode($var,JSON_PRETTY_PRINT);
        },$this->vars);

        return $result;
    }
}

?>