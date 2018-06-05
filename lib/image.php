<?php
namespace framework\lib;

use framework\core\Lib;
use framework\core\Loader;

class Image extends Lib
{
    function load($file,$extension=false){
        $instance=Loader::createInstance("image","lib/image");
        $instance->load($file,$extension);
        return $instance;
    }
}

?>