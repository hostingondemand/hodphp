<?php
namespace framework\modules\developer\model\test;

use framework\lib\model\BaseModel;

class UnitTest extends BaseModel
{
    var $result = array();

    function setupDatabase()
    {
        $this->service->patch->setup();
        $this->service->patch->doPatchProject(true);
        $modules = $this->service->module->getInstalledModules();
        foreach ($modules as $module) {
            $this->service->patch->doPatch($module["name"],true);
        }
    }

    function destroyDatabase()
    {
        $this->service->test->cleanupTables();
    }

    function runTests()
    {
        //load test library
        \framework\core\core()->test;

        //get tests
        $tests = $this->service->test->getTestInstances();
        foreach ($tests as $test) {
            $test->run();
        }
        $this->result = \framework\core\core()->test->getResults();
    }
}
