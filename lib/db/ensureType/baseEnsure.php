<?php
namespace hodphp\lib\db\ensureType;
//This class just tells how an injector should look
abstract class BaseEnsure extends \core\Lib
{
    abstract function ensure($data, $length);
}

