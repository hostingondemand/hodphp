<?php
namespace framework\lib\provider\baseprovider;

use framework\core\Lib;

abstract class BasePatchlogProvider extends Lib
{
    abstract function setup();

    abstract function save($patchModel);

    abstract function needPatch($name);
}

