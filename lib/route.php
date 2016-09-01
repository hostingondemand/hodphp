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

                $renames=$this->config->get("module.rename","route");

                if(is_array($renames)&&isset($renames[$parameters[0]])){
                    $parameters[0]=$renames[$parameters[0]];
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
                $route= explode("/",$this->request->get["route"]);
                $renames=$this->config->get("module.rename","route");
                if(is_array($renames)) {
                    $renames = array_flip($renames);
                    if (isset($renames[$route[0]])) {
                        $route[0] = $renames[$route[0]];
                    }
                }
                return $route;
            }
            return Array();
        }

        function get($key){
            $route = $this->getRoute();
            return $route[$key];
        }
    }
?>