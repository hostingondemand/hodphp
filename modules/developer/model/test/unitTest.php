<?php
namespace modules\developer\model\test;

use core\Controller;
use function core\core;
use lib\model\BaseModel;

class UnitTest extends BaseModel
{
    var $result=array();
    function setupDatabase(){
        $this->service->patch->setup();
        $this->service->patch->doPatchProject();
        $modules=$this->service->module->getInstalledModules();
        foreach($modules as $module){
            $this->service->patch->doPatch($module["name"]);
        }
    }

    function destroyDatabase(){
        $this->service->test->cleanupTables();
    }

    function runTests(){
        //load test library
        core()->test;

        //get tests
        $tests=$this->service->test->getTestInstances();
        foreach($tests as $test){
            $test->run();
        }
        $this->result=core()->test->getResults();
    }
}
