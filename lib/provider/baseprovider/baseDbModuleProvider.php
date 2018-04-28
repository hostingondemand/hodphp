<?php
namespace hodphp\lib\provider\baseprovider;

use hodphp\core\Lib;

abstract class BaseDbModuleProvider extends Lib
{
    function preFetch($query){}
    function prePatchSave($table){}
    function preSaveData(&$data){}
    function preDeleteModel($model){}

}