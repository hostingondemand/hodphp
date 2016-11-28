<?php
namespace lib\db;

use core\Lib;

abstract class BaseMappingProvider extends Lib
{
    abstract function getTableForClass($class);
    abstract function getModelForTable($table);
}