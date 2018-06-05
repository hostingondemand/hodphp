<?php
namespace framework\lib\db\ensureType;
//This class just tells how an injector should look
use framework\core\Lib;

abstract class BaseEnsure extends Lib
{
    abstract function ensure($data, $length);
}

