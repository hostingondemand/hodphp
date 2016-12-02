<?php
namespace lib\patch;

use core\Base;
use core\Lib;

abstract class BasePatch extends  Base
{
    abstract function patch();
}