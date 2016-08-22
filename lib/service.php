<?php
    namespace  lib;
    use core\Lib;
    use core\Loader;

    //simple wrapper to call services
    class Service extends Lib{
        public function __construct()
        {
            Loader::loadClass("baseService","lib\\service");
        }

        public function __get($name)
        {
            return Loader::getSingleton($name, "service");
        }
    }
?>