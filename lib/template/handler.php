<?php

//an abstract representation of an handler
namespace lib\template;
abstract class Handler extends \core\Base
{
    abstract function handle($data = array());


}


?>