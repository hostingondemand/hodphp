<?php
namespace  lib\model;
use core\Base;


abstract class BaseFieldHandler extends Base
{
    var $_model;
    function init($model){
        $this->_model=$model;
    }
    function get($inModel){}

    function set($value){}

    function save(){}
}

?>
