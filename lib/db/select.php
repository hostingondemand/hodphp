<?php
namespace hodphp\lib\db;

use hodphp\core\Lib;
use hodphp\core\Loader;

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
    var $_con;
    var $_noPagination;
    var $_distinct;


    function __construct()
    {

    }

    function distinct(){
        $this->_distinct=true;
        return $this;
    }

    function ignoreParent()
    {
        $this->_ignoreParent = true;
        return $this;
    }
    /**
     * @return \hodphp\lib\db\Select
     */
    function byModel($model, $namespace, $alias = false,$con = "default")
    {
        $this->model = $namespace . "\\" . $model;
        $table = $this->db->tableForModel($model, $namespace);
        $this->table($table, $alias,$con);
        return $this;
    }
    /**
     * @return \hodphp\lib\db\Select
     */
    function table($table, $alias = false, $ignoreModel = false,$con = "default")
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
        $this->_con=$con;
        return $this;

    }
    /**
     * @return \hodphp\lib\db\Select
     */
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
    /**
     * @return \hodphp\lib\db\Select
     */
    function joinModel($model, $namespace, $onLeft, $onRight, $alias)
    {
        $table = $this->db->tableForModel($model, $namespace);
        $this->join($table, $onLeft, $onRight, $alias);
        return $this;
    }
    /**
     * @return \hodphp\lib\db\Select
     */
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
    /**
     * @return \hodphp\lib\db\Select
     */
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
    /**
     * @return \hodphp\lib\db\Select
     */
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
    /**
     * @return \hodphp\lib\db\Select
     */
    function limit($max, $offset = 0)
    {
        $this->_limit = $max;
        $this->_offset = $offset;

        return $this;
    }
    /**
     * @return \hodphp\lib\db\QueryResult[]
     */
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
        $this->executed = $this->db->query($queryString,$this->_con);
    }

    function getQuerystring()
    {
       return $this->db->_provider->createSelectQuery($this);
    }
    /**
     * @return \hodphp\lib\db\Select
     */
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
    /**
     * @return \hodphp\lib\db\QueryResult
     */
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
            $info=$this->getModelInfo();
            $class=$info["class"];
            $namespace=$info["namespace"];
        }
        if ($this->executed === null) {
            $this->execute();
        }
        return $this->executed->fetchModel($class, $namespace);
    }

    function fetchAllModel($class = false, $namespace = false)
    {
        if ($class == false) {
            $info=$this->getModelInfo();
            $class=$info["class"];
            $namespace=$info["namespace"];
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

    function getValue()
    {
        $result = $this->fetch();
        return @array_values($result)[0] ?: false;
    }

    function ignorePagination(){
        $this->_noPagination=true;
    }

    function search($keyword){
        $instance=Loader::createInstance("search","lib/db");
        $instance->initialize($keyword,$this);
        return $instance;
    }

    public function getModelInfo()
    {
        if (!$this->model) {
           $this->model=$this->provider->mapping->default->getModelForTable($this->_table);
        }
        $exp = explode("\\", $this->model);
        $class = $exp[1];
        $namespace = $exp[0];
        return array("class"=>$class, "namespace"=>$namespace);
    }
    function getAllValues(){
        $result=[];
        $fetched=$this->fetchAll();
        if(is_array($fetched)) {
            foreach ($fetched as $fetch) {
                $result[] = @array_values($fetch)[0] ?: false;
            }
        }
        return $result;
    }
}

