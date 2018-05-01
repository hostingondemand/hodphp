<?php

namespace hodphp\provider\dbModule;

use hodphp\core\Base;
use hodphp\core\Loader;
use hodphp\lib\provider\baseprovider\BaseDbModuleProvider;

class SoftDelete extends BaseDbModuleProvider
{
    var $parent = false;


    function prePatchSave($table)
    {
        $table->addField("deleted", "int")->addIndex("deleted");
    }

    function preFetch($query)
    {
        $alias = array_keys($query->_table)[0];
        $query->where($alias . ".deleted!='1' || ".$alias.".deleted IS NULL");
    }

    function preDeleteModel($model,$table=false)
    {
        if(!$model->deleted){
            $model->deleted=1;
            $this->db->saveModel($model,$table);
            return true;
        }

    }



}