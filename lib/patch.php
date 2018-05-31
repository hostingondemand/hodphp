<?php
namespace framework\lib;

use framework\core\Lib;
use framework\core\Loader;

class Patch extends Lib
{
    var $created;

    function addCreated($name)
    {
        $this->created[$name] = $name;
    }

    function getCreated()
    {
        return $this->created;
    }

    function table($name)
    {
        $table = Loader::CreateInstance("table", "lib/patch");
        $table->setName($name);
        return $table;
    }
}

