<?php
namespace hodphp\modules\developer\model;

use hodphp\core\Controller;
use hodphp\lib\model\BaseModel;

class ClearCache extends BaseModel
{
    function clear()
    {
        $this->service->project->removeCache();
    }
}