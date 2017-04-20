<?php
namespace hodphp\lib\patch;

use hodphp\core\Base;

abstract class BasePatch extends Base
{
    abstract function patch();
}