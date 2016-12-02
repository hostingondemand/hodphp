<?php
namespace lib;

use core\Loader;

class Event extends \core\Lib
{

    var $eventListeners = array();
    var $_noCache;

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

    function noCache(){
        $this->_noCache=true;
    }
    function getListenersForEvent($name)
    {
        if (!isset($this->eventListeners[$name]) || $this->_noCache) {
            $this->eventListeners[$name] = array();
            if ($listener = Loader::getSingleton($name, "project\\listener")) {
                $this->eventListeners[$name][] =array("module"=>"","listener"=>$listener);
            }

            if ($listener = Loader::getSingleton($name, "listener")) {
                $this->eventListeners[$name][] =array("module"=>"","listener"=>$listener);
            }

            $dirs = $this->filesystem->getDirs("modules");
            foreach ($dirs as $module) {
                $this->goModule($module);
                if ($listener = Loader::getSingleton($name, "modules/" . $module . "/listener")) {
                    $this->eventListeners[$name][]=array("module"=>$module,"listener"=>$listener);
                }
                $this->goBackModule();
            }
        }
        return $this->eventListeners[$name];

    }
}

?>
