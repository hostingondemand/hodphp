<?php
namespace hodphp\lib;

use hodphp\core\Lib;

class Vendor extends Lib
{
    function load($file)
    {
        $path = $this->filesystem->findRightPath("vendor/" . $file . ".php");
        if ($path) {
            include($path);
        }
    }
}

