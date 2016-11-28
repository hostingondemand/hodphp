<?php
namespace lib;
class Path extends \core\Lib{


    function getApp(){
        return substr(__DIR__,0,-4);
    }

    function getHttp(){
       return $this->config->get("http.path","server");
    }

}
?>