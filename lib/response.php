<?php
    namespace lib;
    class Response extends \core\Lib{

       function write($string,$options=Array()){
            echo $string;
        }

        function renderView($data=Array(),$path=""){
            if(!is_array($data)){
                $path=$data;
                $data=Array();
            }

            if(!$path){
                $path=\core\Loader::$controller."/".(\core\Loader::$action);
            }

            $content=$this->template->parseFile($path,$data);
            $this->write($this->template->parseFile("main",Array("content"=>$content)));
        }


        function renderContent($content){
            $this->write($this->template->parseFile("main",Array("content"=>$content)));
        }

         function renderPartialView($data,$path){
            if(!is_array($data)){
                $path=$data;
            }

            if(!$path){
                $path=\core\Loader::$controller."/".\core\Loader::$action;
            }

            $this->write($this->template->parseFile($path,$data));
        }


        function renderJson($data){
            $this->write($this->serialization->serialize("json",$data));
        }


    }
?>