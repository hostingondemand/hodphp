<?php
namespace hodphp\lib;

use hodphp\core\Lib;
use hodphp\core\Loader;

class Archive extends Lib
{
    function extract($file,$to)
    {
        $file=$this->filesystem->calculatePath($file);
        $to=$this->filesystem->calculatePath($to);
        $type=$this->filesystem->getContentType($file);
        $exp=explode("/",$type);
        $type=$exp[count($exp)-1];
        $extractor=Loader::getSingleton($type,"lib/archive");
        if($extractor){
            $extractor->extract($file,$to);
        }
    }
}

