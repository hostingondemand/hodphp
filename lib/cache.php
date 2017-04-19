<?php
    namespace hodphp\lib;
    use hodphp\core\Lib;

    class Cache extends Lib{

        var $projectSize=0;

        function __construct(){
            if(!$this->filesystem->exists("data/cache")){
                $this->filesystem->mkDir("data/cache");
            }
        }

        function runCachedProject($key,$input,$function){
            if($this->projectSize==0){
                $this->projectSize=$this->filesystem->dirSize("project");
            }
            $filename="data/cache/".$key."_".md5(print_r($input,true)).".php";
            $data=array();
            if($this->filesystem->exists($filename)){
                  $data=  $this->filesystem->getArray($filename);
                  if($data["projectSize"]==$this->projectSize){
                      return $data["content"];
                  }
            }
            $result = $function($input);
            $data["projectSize"]=$this->projectSize;
            $data["content"]=$result;
            $this->filesystem->writeArray($filename,$data);
            return $result;
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


        function destroy(){
            $this->filesystem->rm("data/cache");
            $this->filesystem->mkdir("data/cache");
        }
    }
?>
