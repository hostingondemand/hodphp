<?php
namespace framework\lib\patch;

use framework\core\Base;

abstract class BasePatch extends Base
{
    abstract function patch();
}