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
        $query = "create table `" . $this->name . "` (
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
        $this->db->query($query);

        if (isset($this->actions["addIndex"])) {
            foreach ($this->actions["addIndex"] as $action) {
                $this->db->query("CREATE INDEX `" . $action["name"] . "` ON `" . $this->name . "` (`" . $action["name"] . "`);");
            }
        }


        return $this;
    }

    function update()
    {

        $query = "alter table `" . $this->name . "` ";
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
                $this->db->query("CREATE INDEX `" . $action["name"] . "` ON `" . $this->name . "` (`" . $action["name"] . "`);");
            }
        }
    }

    function exists()
    {
        $query = $this->db->query("SHOW TABLES LIKE '" . $this->name . "';");
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