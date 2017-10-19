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

    function update()
    {
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
                $query .= "MODIFY `" . $action["name"] . "` " . $action["type"];
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
                $this->db->query("CREATE INDEX `" . $action["name"] . "` ON `" . $prefix . $this->name . "` (`" . $action["name"] . "`);");
            }
        }
    }

    function create()
    {
        $prefix = $this->db->getPrefix();
        $query = "create table `" . $prefix . $this->name . "` (
               `id` INT NOT NULL AUTO_INCREMENT,
               `parent_id` int,
               `parent_module` varchar(50)

            ";

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

        $this->patch->addCreated($prefix . $this->name);

        return $this;
    }

    function addField($field, $type)
    {
        $this->actions["addField"][] = array(
            "name" => $field,
            "type" => $type
        );

        return $this;
    }

    function editField($field, $type)
    {
        $this->actions["addField"][] = array(
            "name" => $field,
            "type" => $type
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
        $this->actions["addIndex"][] = array(
            "name" => $field,
        );

        return $this;
    }
}

