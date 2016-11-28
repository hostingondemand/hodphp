<?php
namespace lib;
class Language extends \core\Lib{

    var $data=array();

    function load($file){
        $lang=$this->config->get("language","website");
        if(!$lang){
            $lang="en";
        }

        if(isset($this->data[$file])){
            $this->data[$file]=array_merge($this->data[$file],$this->filesystem->getArray("language/" . $lang . "/" . $file . ".php"));
        }else {
            $this->data[$file] = $this->filesystem->getArray("language/" . $lang . "/" . $file . ".php");
        }

    }

    function get($string,$file=""){
        if($file && !isset($data[$file])){
            $this->load($file);
        }

        if($file && isset($this->data[$file][$string])){
            return $this->data[$file][$string];
        }elseif(!$file){

            foreach($this->data as $val){
                if(isset($val[$string])){
                    return $val[$string];
                }
            }
        }
        return "";
    }

}
?>