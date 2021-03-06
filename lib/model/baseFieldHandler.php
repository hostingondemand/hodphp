<?php
namespace framework\lib\model;
use framework\core\Lib;

abstract class BaseFieldHandler extends Lib
{
    var $_model;
    var $_inModelName;

    function init($model, $fieldName)
    {
        $this->_model = $model;
        $this->_inModelName = $fieldName;
    }

    function fromAnnotation($parameters, $type, $field)
    {
    }

    function get($inModel)
    {
    }

    function set($value)
    {
    }

    function preSave()
    {
    }

    function save()
    {
    }

    function delete()
    {
    }

    function unload(){

    }
}


