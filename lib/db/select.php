<?php
namespace hodphp\lib\db;

use hodphp\core\Lib;

class Select extends Lib
{
    var $_table;
    var $_fields = array();
    var $_joins = array();
    var $_where = array();
    var $_orderBy = array();
    var $_group = array();
    var $_limit = 0;
    var $_offset = 0;
    var $_ignoreParent = false;
    var $executed = null;
    var $model = null;


    function __construct()
    {

    }


    function ignoreParent()
    {
        $this->_ignoreParent = true;
        return $this;
    }

    function byModel($model, $namespace,$alias=false)
    {
        $this->model = $namespace . "\\" . $model;
        $table = $this->db->tableForModel($model,$namespace);
        $this->table($table,$alias);
        return $this;
    }

    function table($table, $alias = false)
    {
        $this->model = $this->provider->mapping->default->getModelForTable($table);

        if (!is_array($table)) {
            if (!$alias) {
                $alias = $table;
            }
            $table = array($alias => $table);
        }
        $this->_table = $table;
        return $this;


    }

    function field($fields)
    {
        if (is_array($fields)) {
            foreach ($fields as $alias => $field) {
                if (is_numeric($alias)) {
                    $alias = $field;
                }
                $this->_fields[$alias] = $field;
            }
        } else {
            $this->_fields[$fields] = $fields;
        }

        return $this;
    }

    function joinModel($model,$namespace, $onLeft, $onRight, $alias){
        $table=$this->db->tableForModel($model,$namespace);
        $this->join($table,$onLeft,$onRight,$alias);
        return $this;
    }

    function join($table, $onLeft, $onRight=false, $alias = false)
    {

        if (!is_array($table)) {
            if (!$alias) {
                $alias = $table;
            }
            $table = array($table => $alias);
        }

        $this->_joins[] = array(
            "table" => $table,
            "left" => $onLeft,
            "right" => $onRight,
        );

        return $this;
    }

    function where($condition)
    {
        $this->_where[] = $condition;

        return $this;
    }

    function orderBy($fields)
    {
        if (is_array($fields)) {
            foreach ($fields as $field => $order) {
                if (is_numeric($field)) {
                    $field = $order;
                    $order = "asc";
                }
                $this->_orderBy[$field] = $order;
            }
        } else {
            $this->_orderBy[$fields] = "asc";
        }

        return $this;
    }

    function group($fields)
    {
        if (is_array($fields)) {
            foreach ($fields as $field => $order) {
                if (is_numeric($field)) {
                    $field = $order;
                    $order = "asc";
                }
                $this->_group[$field] = $order;
            }
        } else {
            $this->_group[$fields] = "asc";
        }

        return $this;
    }

    function limit($max, $offset)
    {
        $this->_limit = $max;
        $this->_offset = $offset;

        return $this;
    }

    function execute()
    {

        $queryString = $this->getQueryString();
        $this->executed = $this->db->query($queryString);
    }

    function getQuerystring()
    {

        $table = array_values($this->_table)[0];
        $alias = array_keys($this->_table)[0];

        $prefix = $this->db->getPrefix();
        if ($this->db->parent && !$this->_ignoreParent) {
            $this->where($alias.".parent_id='" . $this->db->parent["id"] . "' && ".$alias.".parent_module='" . $this->db->parent["module"] . "'");
        }

        $queryString = "select ";

        //fields
        if (count($this->_fields)) {
            $i = 0;
            foreach ($this->_fields as $falias => $field) {
                if ($i) {
                    $queryString .= " , ";
                }
                $queryString .= "" . $this->handleFieldName($field) . "";
                if ($falias != $field) {
                    $queryString .= " as " . $falias;
                }
                $i++;
            }
        } else {
            $queryString .= " * ";
        }


        $queryString .= " from `" . $prefix . $table."`";
        if ($table != $alias) {
            $queryString .= " as " . $alias . "";
        }


        foreach ($this->_joins as $join) {
            $queryString .= " left join ";
            $table = array_keys($join["table"])[0];
            $alias = array_values($join["table"])[0];
            $queryString .= "`" . $prefix . $table . "`";
            if ($alias != $table) {
                $queryString .= " as " . $alias;
            }
            if(is_array($join["left"]) && !$join["right"]){
                $ij=0;
                foreach($join["left"] as $key=>$val){
                    if($ij){
                        $queryString .= " and " . $key . " = " . $val;
                    }else{
                        $queryString .= " on (" . $key . " = " . $val;
                    }
                    $ij++;
                }
                $queryString.=")";

            }else {
                $queryString .= " on " . $join["left"] . " = " . $join["right"];
            }
        }

        if (count($this->_where)) {
            $queryString .= " where (";
            $i = 0;
            foreach ($this->_where as $where) {
                if ($i) {
                    $queryString .= ")and(";
                }
                if (is_callable($where)) {
                    $condition = $this->db->condition();
                    $where($condition);
                    $queryString .= $condition->render();
                } elseif (is_object($where)) {
                    $queryString .= $where->render();
                } else {
                    $queryString .= $where;
                }

                $i++;
            }
            $queryString .= ")";
        }

        if (count($this->_group)) {
            $queryString .= " group by ";
            $i = 0;
            foreach ($this->_group as $groupby => $order) {
                if ($i) {
                    $queryString .= " , ";
                }
                $queryString .= " " . $groupby . " " . $order;
                $i++;
            }
        }

        if (count($this->_orderBy)) {
            $queryString .= " order by ";
            $i = 0;
            foreach ($this->_orderBy as $orderby => $order) {
                if ($i) {
                    $queryString .= " , ";
                }
                $queryString .= $orderby . " " . $order;
                $i++;
            }
        }

        if ($this->limit) {
            $queryString .= " limit ";
            if ($this->_offset) {
                $queryString .= $this->_offset . ",";
            }
            $queryString .= " " . $this->_limit;
        }
        return $queryString;
    }

    function handleFieldName($name)
    {
        if (strpos($name, '(') !== false) {
            return $name;
        }

        $exp = explode(".", $name);
        $corrected = array_map(function ($name) {
            if($name=="*") return $name;
            return "`" . $name . "`";
        }, $exp);
        $name = implode(".", $corrected);
        return $name;
    }

    function fetchAll()
    {
        if ($this->executed === null) {
            $this->execute();
        }
        return $this->executed->fetchAll();
    }

    function fetch()
    {
        if ($this->executed === null) {
            $this->execute();
        }
        return $this->executed->fetch();
    }

    function fetchModel($class = false, $namespace = false)
    {
        if ($class == false) {
            if (!$this->model) {
                $this->provider->mapping->default->getModelForTable($this->_table);
            }
            $exp = explode("\\", $this->model);
            $class = $exp[1];
            $namespace = $exp[0];
        }
        if ($this->executed === null) {
            $this->execute();
        }
        return $this->executed->fetchModel($class, $namespace);
    }

    function fetchAllModel($class = false, $namespace = false)
    {
        if ($class == false) {
            if (!$this->model) {
                $this->provider->mapping->default->getModelForTable($this->_table);
            }
            $exp = explode("\\", $this->model);
            $class = $exp[1];
            $namespace = $exp[0];
        }

        if ($this->executed === null) {
            $this->execute();
        }
        return $this->executed->fetchAllModel($class, $namespace);
    }


    function numRows()
    {
        if ($this->executed === null) {
            $this->execute();
        }
        return $this->executed->numRows();
    }


}

?>