<?php
    namespace core;
    class setup extends Base{
        function setup(){

            $maps=$this->config->get("maps.class","components");
            if(is_array($maps)){
                Loader::$classMaps=$maps;
            }

            $maps=$this->config->get("maps.namespace","components");
            if(is_array($maps)){
                Loader::$classMaps=$maps;
            }

        }
    }