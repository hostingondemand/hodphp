<?php
namespace modules\developer\service;

use core\Controller;
use lib\model\BaseModel;
use lib\service\BaseService;

class Module extends BaseService
{
    var $handled = array();

    function getModules()
    {
        $modules = array_merge(
            $this->getMissingModules(),
            $this->getInstalledModules()
        );
        return $modules;
    }

    function getMissingModules()
    {
        $result = array();
        $required = $this->config->get("requirements.modules", "components");
        foreach ($required as $key => $val) {
            if (!is_array($val)) {
                $val = $this->getModuleByName($val, true);
            }
            if (!$val["installed"]) {
                $result[$val["name"]] = $val;
            }
        }
        return $result;
    }

    function getInstalledModules()
    {
        $result = array();
        $folders = $this->filesystem->getDirs("modules");
        foreach ($folders as $folder) {
            $module = $this->getModuleByName($folder);
            $module["installed"] = true;
            $result[$folder] = $module;
        }


        $folders = $this->filesystem->getDirs("project/modules");
        foreach ($folders as $folder) {
            $module = $this->getModuleByName($folder);
            $result[$folder] = $module;
        }
        return $result;
    }

    function getModuleByName($name, $repositoryOnly = false)
    {

        if (!$repositoryOnly) {
            $modules = $this->config->get("requirements.modules", "components");
            foreach ($modules as $key => $val) {
                if (isset($val["name"]) && $val["name"] == $name) {
                    $module = $val;
                    break;
                }
            }
        }

        if (!isset($module)) {

            $modules = $this->config->get("modules", "_repository");
            if (isset($modules[$name])) {
                $module = $modules[$name];
            }else{
                $module=  array(
                    "name" => $name,
                    "type" => "none"

                );
            }

            $localModules = $this->config->get("modules.local", "repository");
            if(isset($localModules[$name])) {
                $localModule=$localModules[$name];
                if (isset($module) && $module["type"]==$localModule["type"]) {
                    $module["upstream"]=$module["source"];
                    $module["source"]=$localModule["source"];
                } else {
                    $module=$localModule;
                }
            }
        }

        if(isset($module)){
            if($this->filesystem->exists("project/modules/".$name)){
                $module["folder"]="project/modules/".$name;
            }else{
                $module["folder"]="modules/".$name;
            }
            $module["installed"]=$this->isInstalled($name);
        }


        if($this->filesystem->exists("project/modules/".$name)){
            $module["parentFolder"]="project/modules";
            $module["folder"]="project/modules/".$name;
        }else{
            $module["parentFolder"]="modules";
            $module["folder"]="modules/".$name;
        }
        $module["installed"]=$this->isInstalled($name);




        return $module;


    }

    function isInstalled($name)
    {
        return $this->filesystem->exists("modules/" . $name) || $this->filesystem->exists("project/modules/" . $name);
    }

    function install($name)
    {
        $result=false;
        if (!isset($this->handled[$name]) || !$this->handled[$name]) {

            $this->event->raise("modulePreInstall", array("name" => $name));

            $module = $this->getModuleByName($name);
            if ($module) {
                $installService = "method" . ucfirst($module["type"]);
               $result= $this->service->$installService->install($module["name"]);
            }
            $this->handleRequirements($name);
            $this->service->patch->setup();
            $this->service->patch->doPatch($name);
            $this->event->raise("modulePostInstall", array("name" => $name));

            $this->handled[$name] = true;
        }

        return $result;
    }

    function update($name)
    {
        $result=false;
        if (!isset($this->handled[$name]) || !$this->handled[$name]) {
            $this->event->raise("modulePreUpdate", array("name" => $name));

            $module = $this->getModuleByName($name);
            if ($module) {
                $installService = "method" . ucfirst($module["type"]);
                $result=$this->service->$installService->update($module["name"]);
            }

            $this->handleRequirements($name);

            $this->service->patch->setup();
            $this->service->patch->doPatch($name);
            $this->event->raise("modulePostUpdate", array("name" => $name));

            $this->handled[$name] = true;
        }
        return $result;
    }

    function handleRequirements($name)
    {
        $requirementsFile = "modules/" . $name . "/config/requirements.php";
        if ($this->filesystem->exists($requirementsFile)) {
            $arr = $this->filesystem->getArray($requirementsFile);
            if (isset($arr["modules"])) {
                $requirements = $arr["modules"];
            } else {
                $requirements = array();
            }
            foreach ($requirements as $requirement) {
                if ($this->isInstalled($requirement)) {
                    $this->update($requirement);
                } else {
                    $this->install($requirement);
                }
            }
        }
    }
}