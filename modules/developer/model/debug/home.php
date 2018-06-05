<?php
namespace framework\modules\developer\model\debug;

use framework\lib\model\BaseModel;

class Home extends BaseModel
{
    var $stacktraceOn;

    function initialize()
    {
        $this->stackTraceOn = $this->session->_debugStacktrace;
        $this->profileOn = $this->session->_debugProfile;
        return $this;
    }

}
