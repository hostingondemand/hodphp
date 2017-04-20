<?php
namespace hodphp\model;

use hodphp\lib\model\BaseModel;

class Patch extends BaseModel
{

    var $patch;
    var $success;
    var $date;

    function initialize($patch, $success, $date)
    {
        $this->patch = $patch;
        $this->success = $success;
        $this->date = $date;

        return $this;
    }

}

?>