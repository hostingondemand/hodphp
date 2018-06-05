<?php
namespace framework\modules\developer\model;

use framework\lib\model\BaseModel;

class Update extends BaseModel
{
    function update()
    {
        $this->service->project->updateFramework();
        $this->service->project->updateProject();
        $this->service->project->removeCache();
    }
}
