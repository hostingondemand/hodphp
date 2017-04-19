<?php
namespace hodphp\lib;
use hodphp\core\Lib;
use hodphp\core\Loader;

class test extends Lib
{

    var $asserts = array();
    var $currentTest = "";

    function __construct(){
        Loader::loadClass("baseTest","lib/test");
    }
    function assert()
    {
        $assert = Loader::createInstance("assert","lib/test");
        $this->asserts[$this->currentTest][] = $assert;
        return $assert;
    }

    function setCurrentTest($test)
    {
        $this->currentTest = $test;
    }

    function getResults()
    {
        $results = array();
        foreach ($this->asserts as $testName=>$test) {
            foreach ($test as $assert) {
                $results[$testName][] = $assert->results;
            }
        }

        return $results;
    }
}

?>