<?php
namespace hodphp\lib;
    use hodphp\core\Lib;
    use hodphp\core\Loader;

    class Vendor extends Lib{
        function load($file){
            $path=$this->filesystem->findRightPath("vendor/".$file.".php");
            if($path){
                include($path);
            }
        }
    }
?>