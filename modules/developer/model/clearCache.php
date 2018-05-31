<?php
namespace framework\modules\developer\model;

use framework\lib\model\BaseModel;

class ClearCache extends BaseModel
{
    function clear()
    {
        $this->service->project->removeCache();
    }
}