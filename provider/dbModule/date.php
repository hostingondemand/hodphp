<?php

namespace framework\provider\dbModule;

use framework\core\Base;
use framework\core\Loader;
use framework\lib\provider\baseprovider\BaseDbModuleProvider;

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