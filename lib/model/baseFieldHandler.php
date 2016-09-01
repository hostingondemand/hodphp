<?php
namespace  lib\model;
use core\Base;


abstract class BaseFieldHandler extends Base
{
    var $model;
    function init($model){
        $this->model=$model;
    }
    function get($inModel){}

    function set($value){}

    function save(){}
}

?>
