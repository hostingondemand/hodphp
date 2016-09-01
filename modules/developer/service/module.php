<?php
namespace modules\developer\service;

use core\Controller;
use lib\model\BaseModel;
use lib\service\BaseService;

class Module extends BaseService
{
    var $handled=array();
    function getModules(){
       $modules= $this->config->get("requirements.modules","components");
        foreach($modules as $key=>$val){
            if(!is_array($val)){
                $modules[$key]=$this->getModuleByName($val,true);
                $val=$modules[$key];
            }
            $modules[$key]["installed"]=$this->isInstalled($val["name"]);
        }
        return $modules;
    }

    function getModuleByName($name,$reposotoryOnly=false){
        if(!$reposotoryOnly) {
            $modules = $this->config->get("requirements.modules", "components");
            foreach ($modules as $key => $val) {
                if (isset($val["name"]) && $val["name"] == $name) {
                    return $val;
                }
            }
        }

        $modules=$this->config->get("modules","_reposotory");
        if(isset($modules[$name])){
            return $modules[$name];
        }
        return null;
    }

    function isInstalled($name){
        return $this->filesystem->exists("modules/".$name);
    }

    function install($name){
        if(!isset($this->handled[$name]) || !$this->handled[$name]) {
            $this->event->raise("modulePreInstall",array("name"=>$name));

            $module = $this->getModuleByName($name);
            if ($module) {
                $installService = "method" . ucfirst($module["type"]);
                $this->service->$installService->install($module["name"]);
            }
            $this->handleRequirements($name);

            $this->event->raise("modulePostInstall",array("name"=>$name));

            $this->handled[$name] = true;
        }
    }

    function update($name){
        if(!isset($this->handled[$name]) || !$this->handled[$name]) {
            $this->event->raise("modulePreUpdate",array("name"=>$name));

            $module = $this->getModuleByName($name);
            if ($module) {
                $installService = "method" . ucfirst($module["type"]);
                $this->service->$installService->update($module["name"]);
            }

            $this->handleRequirements($name);

            $this->event->raise("modulePostUpdate",array("name"=>$name));

            $this->handled[$name] = true;
        }
    }

    function handleRequirements($name){
        $requirementsFile="modules/".$name."/config/requirements.php";
        if($this->filesystem->exists($requirementsFile)){
            $arr=$this->filesystem->getArray($requirementsFile);
            if(isset($arr["modules"])){
                $requirements=$arr["modules"];
            }else{
                $requirements=array();
            }
            foreach($requirements as $requirement){
                if($this->isInstalled($requirement)) {
                    $this->update($requirement);
                }else{
                    $this->install($requirement);
                }
            }
        }
    }
}