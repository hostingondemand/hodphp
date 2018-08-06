<?php
namespace framework\lib\patch;

use framework\core\Base;

abstract class BasePatch extends Base
{
    function patch(){return true;}
    function noTest(){return true;}
}