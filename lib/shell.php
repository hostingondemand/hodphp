<?php
namespace hodphp\lib;

use hodphp\core\Lib;

class Shell extends Lib
{
    //execute a console command
    function execute($command, $path = null)
    {
        //first set the path to theproject if no path is given
        if (!$path) {
            $path = $this->path->getApp();
        } else {
            $path = $this->filesystem->calculatePath($path);
        }

        //first get the working directory of php
        $cwd = getcwd();

        //temporarely change the working directory
        chdir($path);

        //execute the command
        $result = shell_exec($command);

        //change the working directory back
        chdir($cwd);
        return rtrim($result);
    }
}

?>