<?php
namespace lib\provider\baseprovider;
use core\Base;

abstract class BasePatchlogProvider extends Base
{
    abstract function setup();

    abstract function save($patchModel);

    abstract function needPatch($name);
}

?>