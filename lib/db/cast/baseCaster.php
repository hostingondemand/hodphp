<?php
namespace hodphp\lib\db\cast;
//This class just tells how an injector should look
use hodphp\core\Lib;

abstract class BaseCaster extends Lib
{
    abstract function cast($data, $fromLength, $toLength);
}

