<?php
namespace hodphp\lib;

use hodphp\core\Loader;

class Db extends \hodphp\core\Lib
{
    var $parent = false;
    var $testMode = false;
    var $_provider;
    private $connections;
    private $fields = array();

    function __construct()
    {
        $this->_provider=$this->provider->db->default;
        $this->connectConfigName("default");
    }

    function connectConfigName($name)
    {
        if ($this->config->get("db." . $name . ".host", "server") && $this->config->get("db." . $name . ".username", "server") && $this->config->get("db." . $name . ".db", "server") && !isset($this->connections[$name])) {
            return $this->connect(
                $this->config->get("db." . $name . ".host", "server"),
                $this->config->get("db." . $name . ".username", "server"),
                $this->config->get("db." . $name . ".password", "server"),
                $this->config->get("db." . $name . ".db", "server"),
                $name
            );
        }
        return false;
    }

    function connect($host, $username, $password, $db, $connection)
    {
        return  call_user_func_array(array($this->_provider,"connect"),func_get_args());
    }

    function getPrefix()
    {
        if ($this->testMode) {
            return "test_";
        }
        return "";
    }

    function numRows($query)
    {
        return  call_user_func_array(array($this->_provider,"numRows"),func_get_args());
    }

    function fetchAll($query, $data = null)
    {
        while ($fetch = $this->fetch($query)) {
            $data[] = $fetch;
        }
        return $data;
    }

    function fetch($query)
    {
        return  call_user_func_array(array($this->_provider,"fetch"),func_get_args());
    }

    function cast($data, $from, $to)
    {
        Loader::loadClass("baseCaster", "lib\\db\\cast");

        $from = $this->getCastType($from);
        $to = $this->getCastType($to);
        $caster = Loader::getSingleton($from["type"] . "To" . ucfirst($to["type"]), "lib\\db\\cast");
        if ($caster) {
            $data = $caster->cast($data, $from["length"], $to["length"]);
        }
        return $data;
    }

    private function getCastType($str)
    {
        $exp = explode("(", $str);
        $result["type"] = $exp[0];
        if (count($exp) > 1) {
            $result["length"] = str_replace(")", "", $exp[1]);
        } else {
            $result["length"] = 0;
        }
        return $result;
    }

    function ensureType($data, $type)
    {
        Loader::loadClass("baseEnsure", "lib\\db\\ensureType");

        $type = $this->getCastType($type);
        $ensure = Loader::getSingleton($type["type"], "lib\\db\\ensureType");
        if ($ensure) {
            $data = $ensure->ensure($data, $type["length"]);
        }
        return $data;
    }

    function escape($string, $con = "default")
    {
        return  call_user_func_array(array($this->_provider,"escape"),func_get_args());
    }

    //dummy for now thought this could be useful in the future or when errors occur

    function saveModel($model, $table = false, $ignoreParent = false, $con = "default")
    {
        return  call_user_func_array(array($this->_provider,"saveModel"),func_get_args());
    }


    function query($queryString, $connection = "default")
    {
        return  call_user_func_array(array($this->_provider,"query"),func_get_args());
    }


    function lastId($connection = "default")
    {
        return  call_user_func_array(array($this->_provider,"lastId"),func_get_args());
    }

    function deleteModel($model, $table = false)
    {
        return  call_user_func_array(array($this->_provider,"deleteModel"),func_get_args());
    }

    function execute($queryString, $connection = "default", $params)
    {
        return  call_user_func_array(array($this->_provider,"execute"),func_get_args());
    }

    function refValues($arr)
    {
        if (strnatcmp(phpversion(), '5.3') >= 0) //Reference is required for PHP 5.3+
        {
            $refs = array();
            foreach ($arr as $key => $value)
                $refs[$key] = &$arr[$key];
            return $refs;
        }
        return $arr;
    }

    function select($table, $alias = false,$con = "default")
    {
        $select = Loader::createInstance("select", "lib/db");
        $select->table($table, $alias,false,$con);
        return $select;
    }

    function selectModel($class, $namespace = "", $alias = false,$con = "default")
    {
        $select = Loader::createInstance("select", "lib/db");
        $select->byModel($class, $namespace, $alias,$con);
        return $select;
    }

    function workWithParent($id, $module = false)
    {
        $this->parent = array("id" => $id, "module" => $module ? $module : Loader::$actionModule);
    }

    function startTestMode()
    {
        $this->testMode = true;
    }

    function stopTestMode()
    {
        $this->testMode = false;
    }

    function condition()
    {
        return Loader::createInstance("condition", "lib/db");
    }

    function tableForModel($model, $namespace)
    {
        $modelPath = $namespace . "\\" . $model;
        $table = $this->provider->mapping->default->getTableForClass($modelPath);
        return $table;
    }

    function paginationInfo(){
        return Loader::getSingleton("pagination","lib/db");
    }

    function paginated($function,$perPage,$params=array()){
        $pagination=$this->paginationInfo();
        $pagination->turnOn($perPage);
        $result["result"]=$function($params);
        $result["pagination"]=$pagination->pagination();
        $pagination->turnOff();
        return (object)$result;

    }

    function findMatch($model, $fields = false, $table = false)
    {
        return @$this->findMatches($model, $fields, $table)[0] ?: false;
    }

    function findMatches($model, $fields = false, $table = false)
    {
        $type = $model->_getType();
        $exp = explode('\\', $type);
        $class = array_pop($exp);

        if ($namespace = array_pop($exp) == 'model') {
            $namespace = false;
        }

        $select = (!$table) ? $this->selectModel($class, $namespace) : $this->select($table);

        if (!$fields) {
            $fields = $this->annotation->getFieldsWithAnnotation($type, 'matchable');
        }

        foreach ($fields as $field) {
            if ($model->$field !== null && $model->$field !== '') {
                $select->where("`" . $field . "` = '" . $model->$field . "'");
            }
        }

        return $select->fetchAllModel($class, $namespace);
    }
}


