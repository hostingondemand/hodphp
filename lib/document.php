<?php
namespace lib;

class Document
{

    var $scripts=array();
    var $stylesheets=array();


    function addScript($script,$priority=0){
        $this->scripts[$priority][]=$script;
    }

    function addStylesheet($stylesheet,$priority=0){
        $this->stylesheets[$priority][]=$stylesheet;
    }

    function getScripts(){
        $result=array();
        krsort($this->scripts);
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



}

?>