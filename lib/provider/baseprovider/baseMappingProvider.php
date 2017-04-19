<?php
namespace hodphp\lib\provider\baseprovider;

use hodphp\core\Lib;

abstract class BaseMappingProvider extends Lib
{
    abstract function getTableForClass($class);
    abstract function getModelForTable($table);
}