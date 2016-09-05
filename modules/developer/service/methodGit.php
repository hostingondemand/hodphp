<?php
namespace modules\developer\service;

use core\Controller;
use lib\model\BaseModel;

class MethodGit extends BaseModel
{
    function install($name){
        $module = $this->service->module->getModuleByName($name);
        $this->shell->execute("git clone ".$module["source"]." ".$name,$module["parentFolder"]);
    }


    function update($name){
        $module = $this->service->module->getModuleByName($name);
        $this->shell->execute("git pull origin master", $module["folder"]);
    }
}