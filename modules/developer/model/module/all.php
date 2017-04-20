<?php
namespace hodphp\modules\developer\model\module;

use hodphp\lib\model\BaseModel;

class All extends BaseModel
{
    function Process()
    {
        $this->event->noCache();
        $modules = $this->service->module->getModules();
        foreach ($modules as $module) {
            if ($module["installed"]) {
                $this->model->module->update->process($module["name"]);
            } else {
                $this->model->module->install->process($module["name"]);
            }
        }
    }
}

?>