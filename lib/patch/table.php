<?php
namespace lib\patch;

use core\Lib;

class Table extends Lib
{
    var $actions = array();
    var $name;

    function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    function create()
    {
        $prefix = $this->db->getPrefix();
        $query = "create table `" . $prefix.$this->name . "` (
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
        if($this->db->testMode){
            $query.=" ENGINE = MEMORY;";
        }
        $this->db->query($query);

        if (isset($this->actions["addIndex"])) {
            foreach ($this->actions["addIndex"] as $action) {
                $this->db->query("CREATE INDEX `" . $action["name"] . "` ON `" . $prefix.$this->name . "` (`" . $action["name"] . "`);");
            }
        }

        $this->patch->addCreated($prefix.$this->name);

        return $this;
    }

    function update()
    {
        $prefix = $this->db->getPrefix();
        $query = "alter table `" . $prefix.$this->name . "` ";
        if (isset($this->actions["addField"])) {
            $i = 0;
            foreach ($this->actions["addField"] as $action) {
                if ($i > 0) {
                    $query .= ",";
                }
                $query .= "ADD `" . $action["name"] . "` " . $action["type"];
                $i++;
            }
        }
        $query .= "";
        $this->db->query($query);

        if (isset($this->actions["addIndex"])) {
            foreach ($this->actions["addIndex"] as $action) {
                $this->db->query("CREATE INDEX `" . $action["name"] . "` ON `" . $prefix.$this->name . "` (`" . $action["name"] . "`);");
            }
        }
    }

    function save()
    {
        if($this->exists()) {
            $this->update();
        } else {
            $this->create();
        }
    }

    function exists()
    {
        $prefix = $this->db->getPrefix();
        $query = $this->db->query("SHOW TABLES LIKE '" . $prefix.$this->name . "';");
        return $this->db->numRows($query);
    }

    function addField($field, $type)
    {
        $this->actions["addField"][] = array(
            "name" => $field,
            "type" => $type
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

?>