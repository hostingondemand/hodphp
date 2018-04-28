<?php

namespace hodphp\provider\dbModule;

use hodphp\core\Base;
use hodphp\core\Loader;
use hodphp\lib\provider\baseprovider\BaseDbModuleProvider;

class Date extends BaseDbModuleProvider
{
    var $parent = false;

    function preSaveData(&$data)
    {
        if (!$data["dateCreated"]) {
            $data["dateCreated"] = time();
        }
        $data["dateUpdated"] = time();
    }

    function prePatchSave($table)
    {
        $table->addField("dateCreated", "int")
            ->addField("dateUpdated", "int");
    }

}