<?php
namespace lib\provider\baseprovider;

use core\Lib;

abstract class BaseMappingProvider extends Lib
{
    abstract function getTableForClass($class);
    abstract function getModelForTable($table);
}