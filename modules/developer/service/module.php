<?php
namespace modules\developer\service;

use core\Controller;
use lib\model\BaseModel;

class Module extends BaseModel
{
    function getModules(){
       $modules= $this->config->get("modules","components");
        foreach($modules as $key=>$val){
            $modules[$key]["installed"]=$this->isInstalled($val["name"]);
        }
        return $modules;
    }

    function getModuleByName($name){
        $modules= $this->config->get("modules","components");
        foreach($modules as $key=>$val){
           if($val["name"]==$name){
               return $val;
           }
        }
        return null;
    }

    function isInstalled($name){
        return $this->filesystem->exists("modules/".$name);
    }

    function install($name){
        $module=$this->getModuleByName($name);
        if($module){
            $installService="method".ucfirst($module["type"]);
            $this->service->$installService->install($module["name"]);
        }
    }

    function update($name){
        $module=$this->getModuleByName($name);
        if($module){
            $installService="method".ucfirst($module["type"]);
            $this->service->$installService->update($module["name"]);
        }
    }
}