<?php
namespace hodphp\lib\provider\baseprovider;
use hodphp\core\Base;
use hodphp\core\Lib;

abstract class BasePatchlogProvider extends Lib
{
    abstract function setup();

    abstract function save($patchModel);

    abstract function needPatch($name);
}

?>