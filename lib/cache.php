<?php
    namespace lib;
    use core\Lib;

    class Cache extends Lib{
        function __construct(){
            if(!$this->filesystem->exists("data/cache")){
                $this->filesystem->mkDir("data/cache");
            }
        }

        function runCached($key,$data,$minDate,$function){
            $filename="data/cache/".$key."_".md5(print_r($data,true)).".php";
            if($this->filesystem->exists($filename) && $this->filesystem->getModified($filename)>$minDate){
                return $this->filesystem->getArray($filename);
            }else{
               $result = $function($data);
               $this->filesystem->writeArray($filename,$result);
               return $result;
            }

        }
    }
?>