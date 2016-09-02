<?php
namespace lib;

use core\Loader;

class Event extends \core\Lib
{
    function raise($name,$data){
        Loader::loadClass("baseListener","lib\\event");

        if($listener=Loader::getSingleton($name,"project\\listener")){
            $listener->handle($data);
        }

        $dirs=$this->filesystem->getDirs("modules");
        foreach($dirs as $module) {
            $this->goModule($module);
            if($listener=Loader::getSingleton($name,"modules/".$module."/listener")){
                $listener->handle($data);
            }
           $this->goBackModule();
        }
    }
}

?>