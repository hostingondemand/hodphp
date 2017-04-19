<?php
namespace hodphp\provider\patchlog;

use hodphp\lib\provider\baseprovider\BasePatchlogProvider;

class Db extends BasePatchlogProvider
{
    function setup()
    {
        $table = $this->patch->table("hodpatch");
        if (!$table->exists()) {
            $table->addField("patch", "varchar(50)");
            $table->addField("success", "int");
            $table->addField("date", "int");
            $table->create();
        }
    }

    function save($patchModel)
    {
        $this->db->saveModel($patchModel, "hodpatch");
    }

    function needPatch($name)
    {
        $prefix = $this->db->getPrefix();
        $query = $this->db->query("select id from ".$prefix."hodpatch where patch='" . $name . "' and success=1");
        return !$this->db->numRows($query);
    }
}

?>