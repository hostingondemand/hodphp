<?php
namespace hodphp\lib\db\cast;
//This class just tells how an injector should look
abstract class BaseCaster extends  \core\Lib
{
    abstract function cast($data,$fromLength,$toLength);
}


?>