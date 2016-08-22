<?php
    namespace lib;
    class Route extends \core\lib{
        function createRoute($first=""){
            if(is_array($first)){
                if(!count($first) || !$first[0])
                {
                    return  $this->path->http;
                }
                return $this->path->http."?route=".implode("/",$first);
            }elseif(!$first){
              return  $this->path->http;
            }else{
                return $this->path->http."?route=".implode("/", func_get_args());
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