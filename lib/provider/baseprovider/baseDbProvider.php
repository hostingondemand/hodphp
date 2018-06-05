<?php
namespace framework\lib\provider\baseprovider;
abstract class BaseDbProvider extends \framework\core\Lib
{
    abstract function createSelectQuery($from);
    abstract function connect($host, $username, $password, $db, $connection);
    abstract function numRows($query);
    abstract function fetch($query);
    abstract function escape($string, $con = "default");
    abstract function saveModel($model, $table = false, $ignoreParent = false, $con = "default");
    abstract function query($queryString, $connection = "default");
    abstract function lastId($connection = "default");
    abstract function deleteModel($model, $table = false);
    abstract function execute($queryString, $connection = "default", $params);
}


