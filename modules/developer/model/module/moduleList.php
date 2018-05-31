<?php
namespace framework\modules\developer\model\module;

use framework\lib\model\BaseModel;

class ModuleList extends BaseModel
{
    var $modules;

    function initialize()
    {
        $this->modules = $this->service->module->getModules();
        return $this;
    }
}