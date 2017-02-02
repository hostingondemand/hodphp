<?php
namespace lib\provider\baseprovider;
use core\Base;
use core\Lib;

abstract class BasePatchlogProvider extends Lib
{
    abstract function setup();

    abstract function save($patchModel);

    abstract function needPatch($name);
}

?>