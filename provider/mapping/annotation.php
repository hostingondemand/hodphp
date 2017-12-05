<?php
namespace hodphp\provider\mapping;

use hodphp\core\Loader;
use hodphp\lib\provider\baseprovider\BaseMappingProvider;

class Annotation extends BaseMappingProvider
{

    var $modelToTable = array();
    var $tableToModel = array();

    function __construct()
    {
        $mappings = $this->cache->runCachedProject("mapping", array(), function ($data) {
            $files = $this->filesystem->getProjectFiles('model/', true);

            foreach ($files as $file) {
                $classPath = str_replace(".php", "", $file);
                $exp = explode("/", $classPath);
                $class = $exp[count($exp) - 1];
                $pos = strrpos($file, "/" . $class);
                $fullNamespace = substr_replace($classPath, "", $pos, strlen("/" . $class));
                if ($exp[count($exp) - 2] != "model") {
                    $namespace = $exp[count($exp) - 2];
                } else {
                    $namespace = "";
                }
                $info = Loader::getInfo($class, $fullNamespace, "", true);
                $annotations = $this->annotation->getAnnotationsForClass(@$info->type, "dbTable");
                foreach ($annotations as $annotation) {
                    $translated = $this->annotation->translate($annotation);
                    $item["model"] = lcfirst($namespace) . "\\" . lcfirst($class);
                    $item["modelToLower"] = strtolower($namespace . "\\" . $class);
                    $item["table"] = $translated->parameters[0];
                    $result[] = $item;
                }
            }
            return $result;
        });
        if($mappings) {
            foreach ($mappings as $mapping) {
                $this->modelToTable[$mapping["modelToLower"]] = $mapping["table"];
                $this->tableToModel[$mapping["table"]] = $mapping["model"];
            }
        }
    }

    function getTableForClass($class)
    {
        $class = strtolower($class);
        $exp = explode("\\", $class);
        $class = $exp[count($exp) - 1];
        if ($exp[count($exp) - 2] != "model") {
            $namespace = $exp[count($exp) - 2];
        } else {
            $namespace = "";
        }

        return isset($this->modelToTable[$namespace . "\\" . $class]) ? $this->modelToTable[$namespace . "\\" . $class] : "";
    }

    function getModelForTable($table)
    {
        return isset($this->tableToModel[$table]) ? $this->tableToModel[$table] : "";
    }
}

