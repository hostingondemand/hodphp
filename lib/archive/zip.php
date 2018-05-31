<?php
namespace framework\lib\archive;

//this class is made to handle optional parameters
use framework\core\Lib;

class Zip extends Lib
{
    function extract($from,$to){
        if(class_exists("ZipArchive")){
            $zip = new \ZipArchive;
            $zip->open(  $from );
            $zip->extractTo($to);
            $zip->close();
        }else{
            shell_exec("unzip -o".$from." -d ".$to);
        }
    }
}
?>