<?php
    namespace lib;
    class Route extends \core\lib{
        function createRoute($first=""){
            if(func_num_args()>1){
                $first=func_get_args();
            }
            if(is_array($first)){
                if(!count($first) || !$first[0])
                {
                    return  $this->path->getHttp();
                }
                return $this->path->getHttp()."?route=".implode("/",$first);
            }elseif(!$first){
              return  $this->path->getHttp();
            }else{
                return $this->path->getHttp()."?route=".implode("/", func_get_args());
            }

        }

        function getRoute(){
            if(isset($this->request->get["route"])){
                return explode("/",$this->request->get["route"]);
            }
            return Array();
        }
    }
?>