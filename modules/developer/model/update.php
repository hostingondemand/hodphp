<?php
namespace modules\developer\model;

use core\Controller;
use lib\model\BaseModel;

class Update extends BaseModel
{
    function update()
    {
        $this->service->project->updateFramework();
        $this->service->project->updateProject();
    }
}