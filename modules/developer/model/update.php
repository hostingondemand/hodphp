<?php
namespace hodphp\modules\developer\model;

use hodphp\core\Controller;
use hodphp\lib\model\BaseModel;

class Update extends BaseModel
{
    function update()
    {
        $this->service->project->updateFramework();
        $this->service->project->updateProject();
        $this->service->project->removeCache();
    }
}
