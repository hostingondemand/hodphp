<?php
namespace lib;
use core\Loader;

class Git extends \core\Lib{

    function addRemote($folder, $name,$url){
        $this->shell->execute("git remote add ".$name." ".$url,$folder);
    }

    function removeRemote($folder,$name){
        $this->shell->execute("git remote remove ".$name,$folder);
    }

    function pull($folder,$branch="master",$remote="origin"){
        $this->shell->execute("git pull ".$remote." ".$branch,$folder);
    }

    function init($folder){
        $this->shell->execute("git init",$folder);
    }



}
?>