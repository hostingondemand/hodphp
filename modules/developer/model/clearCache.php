<?php
namespace modules\developer\model;

use core\Controller;
use lib\model\BaseModel;

class ClearCache extends BaseModel
{
    function clear()
    {
        $this->service->project->removeCache();
    }
}