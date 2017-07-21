<?php

namespace hodphp\lib;
use hodphp\core\Lib;

class Thread extends Lib
{
    function wait($time)
    {
        sleep($time);
    }

    function runParallel($data,$function){
        $config=$this->filesystem->getArray("project/config/server.php");

        //prepare some data
        $dir="data/threads";
        $fileName=$dir."/".$this->generateProcessNumber().".php";
        $paramNames=$this->getParamNames($function);
        $input=["config"=>$config,"globals"=>$this->globals->getAll(), "dbParent"=>$this->db->parent,"data"=>$data,"fileName"=>$fileName,"paramNames"=>$paramNames,"session"=>$this->session->getAll()];

        //create dir if missing
        if(!$this->filesystem->exists($dir)){
            $this->filesystem->mkDir($dir);
        }

        //get code to execute
        $code=$this->getCodeOfFunction($function);

        //execute code
        $resultCode=$this->generateCode($code,$input);
        $this->filesystem->clearWrite($fileName,$resultCode);
        $this->execute($fileName);

        $this->debug->info("Started parallel process",array("file"=>$fileName),"thread");
    }

    function getParamNames($funct){
        $result=[];
        $reflFunc = new \ReflectionFunction($funct);
        foreach($reflFunc->getParameters() AS $arg)
        {
            $result[]=$arg->name;
        }
        return $result;
    }

    function getCodeOfFunction($funct){
        $reflFunc = new \ReflectionFunction($funct);
        $start=$reflFunc->getStartLine();
        $end=$reflFunc->getEndLine();
        $content=file_get_contents($reflFunc->getFileName());
        $exp=explode("\n",$content);
        $functionArea=array_slice($exp,$start,$end-$start-1);
        $code=implode("\n",$functionArea);
        return $code;
    }


    function generateProcessNumber(){
        $currenttime = round(microtime(true)*1000,0);
        return $currenttime.mt_rand(1000000,9999999);
    }


    private function execute($filename){
        $filename=$this->filesystem->calculatePath($filename);
        $this->shell->runInBackground("php ".$filename);
    }

    private function generateCode($code,$data){
        $data=var_export($data,true);
        return $this->template->parseFile("codeGeneration/process",["data"=>$data,"code"=>$code]);
    }
}

?>