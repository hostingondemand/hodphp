<?php
namespace framework\modules\developer\model\module;

use framework\lib\model\BaseModel;

class Install extends BaseModel
{
    function Process($name)
    {
        $this->event->noCache();
        $result = $this->service->module->install($name);
        if (is_object($result)) {
            $this->message->send($name . ":" . $result->message, $result->type);
        }
        return $this;
    }
}