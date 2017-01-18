<?php

//an abstract representation of an handler
namespace lib\template;
abstract class Handler extends \core\Lib
{
    abstract function handle($data = array());


}


?>