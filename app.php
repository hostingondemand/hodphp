<?php

class App
{
    function run()
    {

        $this->IncludeCore();
        $this->runAction();

    }


    function IncludeCore()
    {
        include(__DIR__."/core/proxy.php");
        include(__DIR__."/core/base.php");
        include(__DIR__."/core/controller.php");
        include(__DIR__."/core/lib.php");
        include(__DIR__."/core/loader.php");
    }

    function runAction()
    {
        global $argv;
        $routeObj=core\Loader::getSingleton("route","lib");
        core\Loader::loadAction($this->removeOptions($routeObj->getRoute()));
    }

    function removeOptions($args){
        foreach($args as $key=>$val){
            if(substr($val,0,1) =="-"){
                unset($args[$key]);
            }
        }
        $args=array_values($args);
        return $args;
    }

    private function endsWith($searchIn, $searchFor)
    {
        $length = strlen($searchFor);
        if ($length == 0) {
            return true;
        }

        return (substr($searchIn, -$length) === $searchFor);
    }
}

?>