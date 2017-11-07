<?php

namespace hodphp\modules\developer\model\console;

use hodphp\lib\model\BaseModel;

class Patch extends BaseModel
{
    function process()
    {
        $modules = $this->service->module->getModules();
        $this->service->patch->setup();
        foreach($modules as $module){
            $module=$module["name"];
            $this->service->patch->doPatch($module);
        }
        $this->service->patch->doPatch("project");
    }
}

