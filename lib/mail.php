<?php
namespace hodphp\lib;

use hodphp\core\Loader;

class Mail extends \hodphp\core\Lib
{
    function createMessage()
    {
        return Loader::createInstance("message", "lib/mail");
    }
}