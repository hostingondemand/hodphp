<?php
namespace hodphp\modules\developer\model\test;

use hodphp\core\Controller;
use hodphp\lib\model\BaseModel;

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
        \hodphp\core\core()->test;

        //get tests
        $tests=$this->service->test->getTestInstances();
        foreach($tests as $test){
            $test->run();
        }
        $this->result=\hodphp\core\core()->test->getResults();
    }
}
