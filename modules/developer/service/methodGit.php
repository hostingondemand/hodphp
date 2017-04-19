<?php
namespace hodphp\modules\developer\service;

use hodphp\core\Controller;
use hodphp\lib\model\BaseModel;

class MethodGit extends BaseModel
{
    function install($name)
    {
        $module = $this->service->module->getModuleByName($name);
        $folder = $module["parentFolder"] . '/' . $name;
        $this->filesystem->mkdir($folder);
        $this->git->init($folder);
        if(@$module["upstream"]){
            $this->git->addRemote($folder, "upstream", $module["upstream"]);
            $this->git->pull($folder, "master", "upstream");
        }
        $this->git->addRemote($folder, "origin", $module["source"]);
        return $this->git->pull($folder, "master", "origin");
    }


    function update($name)
    {
        $module = $this->service->module->getModuleByName($name);
        $folder = $module["folder"];
        if(@$module["upstream"]){
            $this->git->removeRemote($folder, "upstream");
            $this->git->addRemote($folder, "upstream", $module["upstream"]);
            $this->git->pull($folder, "master", "upstream");
        }
        if(@$module["source"]){
            $this->git->removeRemote($folder,"origin");
            $this->git->addRemote($folder,"origin", $module["source"]);
            return $this->git->pull($folder, "master","origin");
        }
    }
}