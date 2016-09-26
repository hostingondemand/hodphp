<?php
namespace modules\developer\model\module;

use core\Controller;
use lib\model\BaseModel;

class Update extends BaseModel
{
    function Process($name){
        $this->event->noCache();
        $this->service->module->update($name);
        return $this;
    }
}