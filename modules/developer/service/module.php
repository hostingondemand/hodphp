<?php
namespace hodphp\modules\developer\service;

use hodphp\lib\service\BaseService;

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
        if (is_array($required)) {
            foreach ($required as $key => $val) {
                if (!is_array($val)) {
                    $val = $this->getModuleByName($val, true);
                }
                if (!$val["installed"]) {
                    $result[$val["name"]] = $val;
                }
            }
        }
        return $result;
    }

    function getFramework(){
        $moduleType = $this->config->get("moduleManagement.type", "server");
        $framework = $this->config->get("framework", "_repository");
        $localModule = $this->config->get("framework.local", "repository");

        if($moduleType&&isset($framework[$moduleType])){
            $module=$framework[$moduleType];
        }else{
            $module=reset($framework);
        }

;
        if(is_string($localModule)) {
            $module["upstream"] = $module["source"];
            $module["source"]=$localModule; //backwards compatibility
        }elseif(is_array($localModule)){
            $module["upstream"] = $module["source"];
            $module["source"] = $localModule["source"];
        }else{
            $module["source"]=$module;
        }
        $module["name"]="framework";

        $module["folder"]="framework";
        return $module;
    }

    function getModuleByName($name, $repositoryOnly = false)
    {

        if($name=="framework"){
            return $this->getFramework();
        }

        if (!$repositoryOnly) {
            $modules = $this->config->get("requirements.modules", "components");
            if (is_array($modules)) {
                foreach ($modules as $key => $val) {
                    if (isset($val["name"]) && $val["name"] == $name) {
                        $module = $val;
                        break;
                    }
                }
            }
        }

        if (!isset($module)) {

            $modules = $this->config->get("modules", "_repository");
            if (isset($modules[$name])) {
                $module = $modules[$name];
                if(!isset($module["name"])){
                    $moduleType = $this->config->get("moduleManagement.type", "server");
                    if($moduleType&&isset($module[$moduleType])){
                        $module=$module[$moduleType];
                    }else{
                        $module=reset($module);
                    }
                }
            } else {
                $module = array(
                    "name" => $name,
                    "type" => "none"

                );
            }

            $localModules = $this->config->get("modules.local", "repository");
            if (isset($localModules[$name])) {
                $localModule = $localModules[$name];
                if (isset($module) && $module["type"] == $localModule["type"]) {
                    $module["upstream"] = $module["source"];
                    $module["source"] = $localModule["source"];
                } else {
                    $module = $localModule;
                }
            }
        }

        if (isset($module)) {
            if ($this->filesystem->exists("project/modules/" . $name)) {
                $module["folder"] = "project/modules/" . $name;
            } else {
                $module["folder"] = "modules/" . $name;
            }
            $module["installed"] = $this->isInstalled($name);
        }


        if ($this->filesystem->exists("project/modules/" . $name)) {
            $module["parentFolder"] = "project/modules";
            $module["folder"] = "project/modules/" . $name;
        } else {
            $module["parentFolder"] = "modules";
            $module["folder"] = "modules/" . $name;
        }

        if($this->filesystem->exists("project/modules/" . $name) && $this->filesystem->exists("modules/" . $name) )
        {
            $module["installFolder"]= "modules/" . $name;
        }else{
            $module["installFolder"]=$module["folder"];
        }

        $module["installed"] = $this->isInstalled($name);

        return $module;

    }

    function isInstalled($name)
    {
        return $this->filesystem->exists("modules/" . $name) || $this->filesystem->exists("project/modules/" . $name);
    }

    function getInstalledModules()
    {
        $externalModules = $this->config->get("requirements.modules", "components");
        $result = array();
        $folders = $this->filesystem->getDirs("modules");
        foreach ($folders as $folder) {
            if(in_array($folder,$externalModules)) {
                $module = $this->getModuleByName($folder);
                $module["installed"] = true;
                $result[$folder] = $module;
            }
        }

        $folders = $this->filesystem->getDirs("project/modules");
        foreach ($folders as $folder) {
            $module = $this->getModuleByName($folder);
            $result[$folder] = $module;
        }
        return $result;
    }

    function install($name)
    {
        $result = false;
        if (!isset($this->handled[$name]) || !$this->handled[$name]) {

            $this->event->raise("modulePreInstall", array("name" => $name));

            $module = $this->getModuleByName($name);
            if ($module) {
                $installService = "method" . ucfirst($module["type"]);
                $result = $this->service->$installService->install($module["name"]);
            }
            $this->handleRequirements($name);
            $this->service->patch->setup();
            $this->service->patch->doPatch($name);
            $this->event->raise("modulePostInstall", array("name" => $name));

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

    function update($name)
    {
        $result = false;
        if (!isset($this->handled[$name]) || !$this->handled[$name]) {
            $this->event->raise("modulePreUpdate", array("name" => $name));

            $module = $this->getModuleByName($name);
            if ($module) {
                $installService = "method" . ucfirst($module["type"]);
                $result = $this->service->$installService->update($module["name"]);
            }

            $this->handleRequirements($name);

            $this->service->patch->setup();
            $this->service->patch->doPatch($name);
            $this->event->raise("modulePostUpdate", array("name" => $name));

            $this->handled[$name] = true;
        }
        return $result;
    }
}
