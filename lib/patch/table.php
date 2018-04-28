<?php
namespace hodphp\lib\patch;

use hodphp\core\Lib;

class Table extends Lib
{
    var $actions = array();
    var $name;

    function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    function save()
    {

        if ($this->exists()) {
            $this->update();
        } else {
            $this->create();
        }
    }

    function exists()
    {
        $prefix = $this->db->getPrefix();
        $query = $this->db->query("SHOW TABLES LIKE '" . $prefix . $this->name . "';");
        return $this->db->numRows($query);
    }

    function fieldExists($field)
    {
        $prefix = $this->db->getPrefix();
        $query = $this->db->query("SHOW COLUMNS FROM `" . $prefix . $this->name . "` LIKE '" . $field . "';");
        return $this->db->numRows($query);
    }

    function indexExists($field)
    {
        if (!$this->fieldExists($field)) {
            return false;
        }

        $prefix = $this->db->getPrefix();
        $query = $this->db->query("SHOW INDEX FROM `" . $prefix . $this->name . "` WHERE `column_name` = '" . $field . "';");
        return $this->db->numRows($query);
    }

    function update()
    {

        foreach($this->db->getModules() as $module){
            if($module->prePatchSave($this)){
                return false;
            }
        }

        $prefix = $this->db->getPrefix();
        $query = "ALTER TABLE `" . $prefix . $this->name . "` ";
        $i = 0;
        if (isset($this->actions["addField"])) {
            foreach ($this->actions["addField"] as $action) {
                if ($i > 0) {
                    $query .= ",";
                }
                $query .= "ADD `" . $action["name"] . "` " . $action["type"];
                $i++;
            }
        }
        if (isset($this->actions["editField"])) {
            foreach ($this->actions["editField"] as $action) {
                if ($i > 0) {
                    $query .= ",";
                }
                $query .= (!empty($action["newName"]) ? "CHANGE" : "MODIFY") . " `" . $action["name"] . "` " . (!empty($action["newName"]) ? "`" . $action["newName"] . "` " : "") . $action["type"];
                $i++;
            }
        }
        if (isset($this->actions["removeField"])) {
            foreach ($this->actions["removeField"] as $action) {
                if ($i > 0) {
                    $query .= ",";
                }
                $query .= "DROP `" . $action["name"] . "`";
                $i++;
            }
        }
        $query .= "";
        $this->db->query($query);

        if (isset($this->actions["addIndex"])) {
            foreach ($this->actions["addIndex"] as $action) {
                if (!$this->indexExists($action['name'])) {
                    $this->db->query("CREATE INDEX `" . $action["name"] . "` ON `" . $prefix . $this->name . "` (`" . $action["name"] . "`);");
                }
            }
        }

        if (isset($this->actions["setEncoding"])) {
            foreach ($this->actions["setEncoding"] as $action) {
                $this->db->query("ALTER TABLE `" . $prefix . $this->name['table_name'] . "` CONVERT TO CHARACTER SET '" . $action["name"] ."'");
            }
        }
    }

    function create()
    {
        foreach($this->db->getModules() as $module){
            if($module->prePatchSave($this)){
                return false;
            }
        }

        $prefix = $this->db->getPrefix();
        $query = "CREATE TABLE `" . $prefix . $this->name . "` (`id` INT NOT NULL AUTO_INCREMENT";

        if (isset($this->actions["addField"])) {
            foreach ($this->actions["addField"] as $action) {
                $query .= ",`" . $action["name"] . "` " . $action["type"];
            }
        }
        $query .= ",PRIMARY KEY (id))";
        if ($this->db->testMode) {
            $query .= " ENGINE = MEMORY;";
        }
        $this->db->query($query);

        if (isset($this->actions["addIndex"])) {
            foreach ($this->actions["addIndex"] as $action) {
                $this->db->query("CREATE INDEX `" . $action["name"] . "` ON `" . $prefix . $this->name . "` (`" . $action["name"] . "`);");
            }
        }

        if (isset($this->actions["setEncoding"])) {
            foreach ($this->actions["setEncoding"] as $action) {
                $this->db->query("ALTER TABLE `" . $prefix . $this->name['table_name'] . "` CONVERT TO CHARACTER SET '" . $action["name"] ."'");
            }
        }

        $this->patch->addCreated($prefix . $this->name);

        return $this;
    }

    function addField($field, $type)
    {
        if ($this->fieldExists($field)) {
            return $this;
        }

        $this->actions["addField"][] = array(
            "name" => $field,
            "type" => $type
        );

        return $this;
    }

    function editField($field, $type, $newName = false)
    {
        if (!$this->fieldExists($field)) {
            return $this;
        }

        $this->actions["editField"][] = array(
            "name" => $field,
            "type" => $type,
            "newName" => $newName
        );

        return $this;
    }

    function removeField($field)
    {
        if (!$this->fieldExists($field)) {
            return $this;
        }

        $this->actions["removeField"][] = array(
            "name" => $field,
        );

        return $this;
    }

    function addIndex($field)
    {
        if (!$this->fieldExists($field)) {
            return $this;
        }

        $this->actions["addIndex"][] = array(
            "name" => $field,
        );

        return $this;
    }

    function setEncoding($encoding)
    {
        $this->actions["setEncoding"][] = array(
            "name" => $encoding,
        );

        return $this;
    }
}

