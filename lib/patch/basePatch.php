<?php
namespace hodphp\lib\patch;

use hodphp\core\Base;
use hodphp\core\Lib;

abstract class BasePatch extends  Base
{
    abstract function patch();
}