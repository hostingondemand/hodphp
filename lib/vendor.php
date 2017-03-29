<?php
namespace lib;
    use core\Lib;
    use core\Loader;

    class Vendor extends Lib{
        function load($file){
            $path=$this->filesystem->findRightPath("vendor/".$file.".php");
            if($path){
                include($path);
            }
        }
    }
?>