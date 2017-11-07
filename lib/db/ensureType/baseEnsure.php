<?php
namespace hodphp\lib\db\ensureType;
//This class just tells how an injector should look
use hodphp\core\Lib;

abstract class BaseEnsure extends Lib
{
    abstract function ensure($data, $length);
}

