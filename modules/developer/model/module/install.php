<?php
namespace modules\developer\model\module;

use core\Controller;
use lib\model\BaseModel;

class Install extends BaseModel
{
    function Process($name){
        $this->event->noCache();
        $this->service->module->install($name);
        return $this;
    }
}