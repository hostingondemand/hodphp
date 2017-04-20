<?php

//an abstract representation of an handler
namespace hodphp\lib\template;
abstract class Handler extends \hodphp\core\Lib
{
    abstract function handle($data = array());

}

?>