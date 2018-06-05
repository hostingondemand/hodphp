<?php
namespace framework\lib;

use framework\core\Loader;

class Validation extends \framework\core\Lib
{

    function __construct()
    {
        $this->language->load("_validation");
        Loader::LoadClass("BaseValidator", "lib/validation");
    }

    function validator($name)
    {
        return $this->provider->validator->$name();
    }

}

