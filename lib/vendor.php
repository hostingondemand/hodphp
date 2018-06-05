<?php
namespace framework\lib;

use framework\core\Lib;

class Vendor extends Lib
{
    function load($file)
    {
        $path = $this->filesystem->findRightPath("vendor/" . $file . ".php");
        if ($path) {
            $this->debug->info("Loaded external library",array("file"=>$file),"file");
            include($path);
        }else{
            $this->debug->error("Failed to run external library",array("file"=>$file),"file");
        }
    }
}

