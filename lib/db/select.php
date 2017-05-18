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

    function byModel($model, $namespace, $alias = false)
    {
        $this->model = $namespace . "\\" . $model;
        $table = $this->db->tableForModel($model, $namespace);
        $this->table($table, $alias);
        return $this;
    }

    function table($table, $alias = false, $ignoreModel = false)
    {
        if (!$ignoreModel) {
            $this->model = $this->provider->mapping->default->getModelForTable($table);
        }
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

    function joinModel($model, $namespace, $onLeft, $onRight, $alias)
    {
        $table = $this->db->tableForModel($model, $namespace);
        $this->join($table, $onLeft, $onRight, $alias);
        return $this;
    }

    function join($table, $onLeft, $onRight = false, $alias = false)
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

    function limit($max, $offset = 0)
    {
        $this->_limit = $max;
        $this->_offset = $offset;

        return $this;
    }

    function fetchAll()
    {
        if ($this->executed === null) {
            $this->execute();
        }
        return $this->executed->fetchAll();
    }

    function execute()
    {

        $queryString = $this->getQueryString();
        $this->executed = $this->db->query($queryString);
    }

    function getQuerystring()
    {
        $pagination = $this->db->paginationInfo();
        $table = array_values($this->_table)[0];
        $alias = array_keys($this->_table)[0];

        $prefix = $this->db->getPrefix();
        if ($this->db->parent && !$this->_ignoreParent) {
            $this->where($alias . ".parent_id='" . $this->db->parent["id"] . "' && " . $alias . ".parent_module='" . $this->db->parent["module"] . "'");
        }

        $queryString = "select ";
        $pagination->query = "select count(*) as amount";

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

        $add = " from `" . $prefix . $table . "`";
        $queryString .= $add;
        $pagination->query .= $add;

        if ($table != $alias) {
            $add = " as " . $alias . "";
            $queryString .= $add;
            $pagination->query .= $add;
        }

        foreach ($this->_joins as $join) {
            $add = " left join ";
            $queryString .= $add;
            $pagination->query .= $add;

            $table = array_keys($join["table"])[0];
            $alias = array_values($join["table"])[0];

            $add = "`" . $prefix . $table . "`";
            $queryString .= $add;
            $pagination->query .= $add;

            if ($alias != $table) {
                $add = " as " . $alias;
                $queryString .= $add;
                $pagination->query .= $add;
            }
            if (is_array($join["left"]) && !$join["right"]) {
                $ij = 0;
                foreach ($join["left"] as $key => $val) {
                    if ($ij) {
                        $add = " and " . $key . " = " . $val;
                    } else {
                        $add = " on (" . $key . " = " . $val;
                    }
                    $queryString .= $add;
                    $pagination->query .= $add;
                    $ij++;
                }
                $add = ")";
                $queryString .= $add;
                $pagination->query .= $add;

            } else {
                $add = " on " . $join["left"] . " = " . $join["right"];
                $queryString .= $add;
                $pagination->query .= $add;
            }
        }

        if (count($this->_where)) {
            $add = " where (";
            $queryString .= $add;
            $pagination->query .= $add;

            $i = 0;
            foreach ($this->_where as $where) {
                if ($i) {
                    $add = ")and(";
                    $queryString .= $add;
                    $pagination->query .= $add;
                }
                if (is_callable($where)) {
                    $condition = $this->db->condition();
                    $where($condition);
                    $add = $condition->render();
                    $queryString .= $add;
                    $pagination->query .= $add;
                } elseif (is_object($where)) {
                    $add = $where->render();
                    $queryString .= $add;
                    $pagination->query .= $add;
                } else {
                    $add = $where;
                    $queryString .= $add;
                    $pagination->query .= $add;
                }

                $i++;
            }
            $add = ")";
            $queryString .= $add;
            $pagination->query .= $add;
        }

        if (count($this->_group)) {
            $add = " group by ";
            $queryString .= $add;
            $pagination->query .= $add;

            $i = 0;
            foreach ($this->_group as $groupby => $order) {
                if ($i) {
                    $add = " , ";
                    $queryString .= $add;
                    $pagination->query .= $add;
                }
                $add = " " . $groupby . " " . $order;
                $queryString .= $add;
                $pagination->query .= $add;
                $i++;
            }
        }

        if (count($this->_orderBy)) {
            $add = " order by ";
            $queryString .= $add;
            $pagination->query .= $add;
            $i = 0;
            foreach ($this->_orderBy as $orderby => $order) {
                if ($i) {
                    $add = " , ";
                    $queryString .= $add;
                    $pagination->query .= $add;
                }
                $add = $orderby . " " . $order;
                $queryString .= $add;
                $pagination->query .= $add;
                $i++;
            }
        }

        if ($this->_limit) {
            $queryString .= " limit ";
            if ($this->_offset) {
                $queryString .= $this->_offset . ",";
            }
            $queryString .= " " . $this->_limit;
        } elseif ($pagination->status) {
            $queryString .= " limit " . $pagination->getLimit();
        }

        return $queryString;
    }

    function where($condition)
    {
        $this->_where[] = $condition;

        return $this;
    }

    function handleFieldName($name)
    {
        if (strpos($name, '(') !== false) {
            return $name;
        }

        $exp = explode(".", $name);
        $corrected = array_map(function ($name) {
            if ($name == "*") return $name;
            return "`" . $name . "`";
        }, $exp);
        $name = implode(".", $corrected);
        return $name;
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

