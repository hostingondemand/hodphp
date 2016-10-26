<?php
namespace lib;

use core\Loader;

class Mail extends \core\Lib
{
    function createMessage(){
        return Loader::createInstance("message","lib/mail");
    }
}