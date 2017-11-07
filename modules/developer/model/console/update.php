<?php

namespace hodphp\modules\developer\model\console;

use hodphp\lib\model\BaseModel;

class Update extends BaseModel
{
    function process($module = null)
    {
        if ($module) {
            $moduleInfo = $this->service->module->getModuleByName($module);
            if ($moduleInfo["installed"]) {
                $this->model->module->update->process($module);
            } else {
                $this->model->module->install->process($module);
            }
        } else {
            $this->service->project->updateFramework();
            $this->service->project->updateProject();
            $this->model->module->all->process();
            $this->service->project->removeCache();
        }
    }
}

