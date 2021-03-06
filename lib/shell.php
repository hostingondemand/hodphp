<?php
namespace framework\lib;

use framework\core\Lib;

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

        $this->debug->info("Ran shell command", array("input"=>$command,"path"=>$path?:"no","result"=>rtrim($result)),"shell");

        return rtrim($result);
    }


    function runInBackground($command)
    {
        $proc = proc_open($command . "> /dev/null &",
            array(
                array('pipe', 'r'),
                array('pipe', 'w')),
            $pipes);
         fread($pipes[1],100);
        array_map('fclose',$pipes);
        proc_close($proc);
        $this->debug->info("Ran shell command in background", array("input"=>$command),"shell");
    }

    function commandExists($command){
        $result=shell_exec(sprintf("which %s", escapeshellarg($command)));
        return !empty($result);
    }

    function executeWithInput($command,$input){
        $proc = proc_open($command,
            array(
                array('pipe', 'r'),
                array('pipe', 'w')),
            $pipes);
        fwrite($pipes[0],$input);
        fclose($pipes[0]);
        unset($pipes[0]);
        $output=fread($pipes[1],255);
        array_map('fclose',$pipes);
        proc_close($proc);
        $this->debug->info("Ran shell command with file input", array("input"=>$command),"shell");
        return $output;
    }
}

