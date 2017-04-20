<?php
namespace hodphp\lib\test;
abstract class BaseTest extends \hodphp\core\Base
{
    //dummy setup.. in case no setup is needed
    function run()
    {
        $this->setup();
        $type = $this->_getType();
        $methods = get_class_methods($type);
        foreach ($methods as $method) {
            if ($this->annotation->methodHasAnnotations($type, $method, "test")) {
                $exp = explode("\\", $type);
                $className = $exp[count($exp) - 1];
                $this->test->setCurrentTest($className . "->" . $method . "");

                $this->$method();
            }
        }
    }

    function setup()
    {
    }
}

?>