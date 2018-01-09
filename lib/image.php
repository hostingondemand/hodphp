<?php
namespace hodphp\lib;

use hodphp\core\Lib;
use hodphp\core\Loader;

class Image extends Lib
{
    function load($file,$extension=false){
        $instance=Loader::createInstance("image","lib/image");
        $instance->load($file,$extension);
        return $instance;
    }
}

?>