<?php

//an abstract representation of an handler
namespace framework\lib\template;
abstract class Handler extends \framework\core\Lib
{
    abstract function handle($data = array());

}

