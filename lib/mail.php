<?php
namespace framework\lib;

use framework\core\Loader;

class Mail extends \framework\core\Lib
{
    function createMessage()
    {
        return Loader::createInstance("message", "lib/mail");
    }
}