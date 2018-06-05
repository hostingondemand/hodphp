<?php
namespace framework\lib\provider\baseprovider;

use framework\core\Lib;

abstract class BaseDbModuleProvider extends Lib
{
    function preFetch($query){}
    function prePatchSave($table){}
    function preSaveData(&$data){}
    function preDeleteModel($model){}

}