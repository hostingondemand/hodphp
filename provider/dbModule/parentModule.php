<?php

namespace hodphp\provider\dbModule;

use hodphp\core\Base;
use hodphp\core\Loader;
use hodphp\lib\provider\baseprovider\BaseDbModuleProvider;

class parentModule extends BaseDbModuleProvider
{
    var $parent = false;

    function preFetch($query)
    {
        $alias = array_keys($query->_table)[0];
        if ($this->parent && $this->parent["id"] && $this->parent["module"]) {
            $query->where($alias . ".parent_id='" . $this->parent["id"] . "' && " . $alias . ".parent_module='" . $this->parent["module"] . "'");
        }
    }

    function prePatchSave($table)
    {
        $table->addField("parent_id", "int")->addIndex("parent_id")
            ->addField("parent_module", "varchar(50)")->addIndex("parent_module");
    }

    function preSaveData(&$data)
    {
        if (!$data["id"] && $this->parent) {
            $data["parent_id"] = $this->parent["id"];
            $data["parent_module"] = $this->parent["module"];
        }

    }


    function workWithParent($id, $module = false)
    {
        $this->parent = array("id" => $id, "module" => $module ? $module : Loader::$actionModule);
    }
}