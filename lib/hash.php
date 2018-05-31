<?php
namespace framework\lib;

use framework\core\Lib;

class Hash extends Lib
{

    function __call($name, $arguments)
    {
        return hash($name, $arguments[0]);
    }

}

