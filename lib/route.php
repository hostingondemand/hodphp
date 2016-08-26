<?php
    namespace lib;
    class Route extends \core\lib{
        function createRoute($first=""){


            if(func_num_args()>1){
                $first=func_get_args();
            }
            if(is_array($first)){

                foreach($first as $key=>$val){
                    if(!$val){
                        $parameters[$key]=$this->route->get($key);
                    }else{
                        $parameters[$key]=$val;
                    }
                }

                return $this->path->getHttp()."?route=".implode("/",$parameters);
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

        function get($key){
            $route = $this->getRoute();
            return $route[$key];
        }
    }
?>