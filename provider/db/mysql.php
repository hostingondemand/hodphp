<?php
namespace hodphp\provider\db;

use hodphp\core\Loader;
use hodphp\lib\provider\baseprovider\BaseDbProvider;

class Mysql extends BaseDbProvider
{
    private $connections;
    private $fields = array();


    function createSelectQuery($from){
        $pagination = $this->db->paginationInfo();
        $table = array_values($from->_table)[0];
        $alias = array_keys($from->_table)[0];

        $prefix = $this->db->getPrefix();
        if ($from->db->parent && !$from->_ignoreParent) {
            $from->where($alias . ".parent_id='" . $from->db->parent["id"] . "' && " . $alias . ".parent_module='" . $from->db->parent["module"] . "'");
        }

        $queryString = "select ";
        $pagination->query = "select count(*) as amount";

        //fields
        if (count($from->_fields)) {
            $i = 0;
            foreach ($from->_fields as $falias => $field) {
                if ($i) {
                    $queryString .= " , ";
                }
                $queryString .= "" . $from->handleFieldName($field) . "";
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

        foreach ($from->_joins as $join) {
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

        if (count($from->_where)) {
            $add = " where (";
            $queryString .= $add;
            $pagination->query .= $add;

            $i = 0;
            foreach ($from->_where as $where) {
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

        if (count($from->_group)) {
            $add = " group by ";
            $queryString .= $add;
            $pagination->query .= $add;

            $i = 0;
            foreach ($from->_group as $groupby => $order) {
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

        if (count($from->_orderBy)) {
            $add = " order by ";
            $queryString .= $add;
            $pagination->query .= $add;
            $i = 0;
            foreach ($from->_orderBy as $orderby => $order) {
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

        if ($from->_limit) {
            $queryString .= " limit ";
            if ($from->_offset) {
                $queryString .= $from->_offset . ",";
            }
            $queryString .= " " . $from->_limit;
        } elseif ($pagination->status) {
            $queryString .= " limit " . $pagination->getLimit();
        }

        return $queryString;
    }

    function connect($host, $username, $password, $db, $connection)
    {
        if ($this->connections[$connection] = new \mysqli($host, $username, $password, $db)) {
            return true;
        }
        return false;
    }


    function numRows($query)
    {
        return mysqli_num_rows($query->result);
    }

    function fetch($query)
    {
        return mysqli_fetch_assoc($query->result);
    }


    function escape($string, $con = "default")
    {
        return str_replace("'","\\'",$string);
    }


    function saveModel($model, $table = false, $ignoreParent = false, $con = "default")
    {
        if($model->hasMethod('_preSave')) {
            $model->_preSave();
        }

        $prefix = $this->db->getPrefix();
        if (!$table) {
            $table = $this->provider->mapping->default->getTableForClass($model->_getType());
        }
        if ($this->db->parent && !$ignoreParent) {
            $model->parent_id = $this->db->parent["id"];
            $model->parent_module = $this->db->parent["module"];
        }

        if (!isset($this->fields[$table])) {
            $this->fields[$table] = $this->query("SHOW columns FROM `" . $prefix . $table . "`", $con)->fetchAll();
        }

        if ($model->id) {
            $this->updateModel($model, $table, $con);
            $mode = "update";
            $id = $this->db->lastId();
        } else {
            $this->insertModel($model, $table, $con);
            $mode = "insert";
            $id = $model->id;
        }

        return array("mode" => $mode, "id" => $id);
    }

    function updateModel($model, $table, $con)
    {
        $prefix = $this->db->getPrefix();
        if ((method_exists($model, "_isInvalidated") && $model->_isInvalidated()) || !method_exists($model, "_isInvalidated")) {
            $query = "update `" . $prefix . $table . "` set ";
            $data = $model->toArray();
            $i = 0;
            foreach ($this->fields[$table] as $field) {
                $fieldName = $field["Field"];
                if (isset($data[$fieldName])) {
                    if ($i) {
                        $query .= " , ";
                    }
                    $input=$data[$fieldName];
                    if(!$input ){
                    }
                    $query .= "`" . $fieldName . "`='" . $this->escape($data[$fieldName]) . "' ";
                    $i++;
                }
            }
            $query .= " where id='" . $data["id"] . "'";
            $this->query($query, $con);
            $model->_saved();
        }
    }

    function query($queryString, $connection = "default")
    {
        if (!isset($this->connections[$connection])) {
            $this->db->connectConfigName($connection); //to avoid manual connecting  a lot
        }

        $query = $this->connections[$connection]->query($queryString);
        if (!$query) {
            $error = $this->connections[$connection]->error;
            $this->debug->error("SQL Query:" . $error, array(
                "error" => $error,
                "connection" => $connection,
                "query" => $queryString
            ));
        }
        $result = Loader::createInstance("queryResult", "lib\db");
        $result->result = $query;

        return $result;
    }

    function insertModel($model, $table, $con)
    {
        $prefix = $this->db->getPrefix();
        $query = "insert into `" . $prefix . $table . "` set id=null";
        $data = $model->toArray();
        foreach ($this->fields[$table] as $field) {
            $fieldName = $field["Field"];
            if (isset($data[$fieldName]) && !is_array($data[$fieldName]) && !is_array($fieldName) && $fieldName != "id") {
                $query .= " , ";
                $query .= "`" . $fieldName . "`='" . $this->escape($data[$fieldName]) . "' ";
            }
        }

        $q = $this->query($query, $con);
        $model->id = $this->lastId($con);
        $model->_saved();

    }

    function lastId($connection = "default")
    {
        return MySqli_Insert_Id($this->connections[$connection]);
    }

    function deleteModel($model, $table = false)
    {
        if (!$table) {
            $table = $this->provider->mapping->default->getTableForClass($model->_getType());
        }
        $this->query("delete from `" . $table . "` where id='" . $model->id . "'");
        try {
            $model->_deleted();
        }catch(Exception $ex){}
    }

    function execute($queryString, $connection = "default", $params)
    {
        if (!isset($this->connections[$connection])) {
            $this->db->connectConfigName($connection); //to avoid manual connecting  a lot
        }

        if ($query = $this->connections[$connection]->prepare($queryString)) {
            $prefix = array(str_repeat("s", count($params)));
            $merged = array_merge($prefix, $params);
            $bound = call_user_func_array(array($query, "bind_param"), $this->db->refValues($merged));

            $result = $query->execute();
            if (!$result) {
                $this->debug->error("SQL Execute:" . $query->error, array(
                    "error" => $query->error,
                    "connection" => $connection,
                    "query" => $queryString
                ));
            }

            return $result;
        }

        return false;

    }
}


