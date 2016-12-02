<?php
namespace lib;
    use core\Lib;
    use core\Loader;

    class Patch extends Lib{
        function table($name){
            $table= Loader::CreateInstance("table","lib/patch");
            $table->setName($name);
            return $table;
        }
    }
?>