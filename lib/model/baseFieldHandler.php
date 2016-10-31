<?php
namespace  lib\model;
use core\Base;


abstract class BaseFieldHandler extends Base
{
    var $_model;
    var $_inModelName;
    function init($model,$fieldName){
        $this->_model=$model;
        $this->_inModelName=$fieldName;
    }
    function get($inModel){}

    function set($value){}

    function save(){}

    function delete(){}
}

?>
