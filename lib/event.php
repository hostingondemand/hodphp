<?php
namespace lib;

use core\Loader;

class Event extends \core\Lib
{

    var $eventListeners = array();

    function raise($name, $data)
    {
        Loader::loadClass("baseListener", "lib\\event");
        $listeners=$this->getListenersForEvent($name);
        foreach($listeners as $listener){
            $this->goModule($listener["module"]);
            $result=$listener["listener"]->handle($data);
            if($result){
                $data=$result;
            }
            $this->goBackModule();
        }

        return $data;
    }

    function getListenersForEvent($name)
    {
        if (!isset($eventListeners[$name])) {
            $eventListeners[$name] = array();
            if ($listener = Loader::getSingleton($name, "project\\listener")) {
                $eventListeners[$name][] =array("module"=>"","listener"=>$listener);
            }

            $dirs = $this->filesystem->getDirs("modules");
            foreach ($dirs as $module) {
                $this->goModule($module);
                if ($listener = Loader::getSingleton($name, "modules/" . $module . "/listener")) {
                    $eventListeners[$name][]=array("module"=>$module,"listener"=>$listener);
                }
                $this->goBackModule();
            }
        }
        return $eventListeners[$name];

    }
}

?>