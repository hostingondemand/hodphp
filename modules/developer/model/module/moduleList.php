<?php
namespace hodphp\modules\developer\model\module;

use hodphp\core\Controller;
use hodphp\lib\model\BaseModel;

class ModuleList extends BaseModel
{
    var $modules;
    function initialize(){
        $this->modules=$this->service->module->getModules();
        return $this;
    }
}