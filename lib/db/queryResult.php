<?php
namespace framework\lib\db;

use framework\core\Lib;

class QueryResult extends Lib
{
    var $result;

    function fetchAll()
    {
        return $this->db->fetchAll($this);
    }

    function fetchAllModel($class, $namespace = false)
    {
        $result = [];
        while ($model = $this->fetchModel($class, $namespace)) {
            $result[] = $model;
        }
        return $result;
    }

    function fetchModel($class, $namespace = false)
    {
        $data = $this->fetch();
        if ($data) {
            if ($namespace) {
                return $this->model->$namespace->$class->fromArray($data);
            } else {
                return $this->model->$class->fromArray($data);
            }
        }
        return false;
    }

    function fetch()
    {
        return $this->db->fetch($this);
    }

    function numRows()
    {
        return $this->db->numRows($this);
    }

}

