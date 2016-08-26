<?php
namespace modules\developer\model\module;

use core\Controller;
use lib\model\BaseModel;

class ModuleList extends BaseModel
{
    var $modules;
    function initialize(){
        $this->modules=$this->service->module->getModules();
        return $this;
    }
}