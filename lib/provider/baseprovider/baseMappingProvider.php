<?php
namespace framework\lib\provider\baseprovider;

use framework\core\Lib;

abstract class BaseMappingProvider extends Lib
{
    abstract function getTableForClass($class);

    abstract function getModelForTable($table);
}