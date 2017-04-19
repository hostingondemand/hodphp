<?php
namespace hodphp\provider\mapping;
use hodphp\lib\provider\baseprovider\BaseMappingProvider;

class Config extends BaseMappingProvider{

    var $modelToTable=array();
    var $tableToModel=array();

    function __construct(){
        $mappings=$this->config->get("tables","mapping");
        if(is_array($mappings)){
            foreach($mappings as $mapping){
                $this->modelToTable[$mapping["model"]]=$mapping["table"];
                $this->tableToModel[$mapping["table"]]=$mapping["model"];
            }
        }
    }

    function getTableForClass($class)
    {
        $class=strtolower($class);
        $exp = explode("\\", $class);
        $class = $exp[count($exp) - 1];
        if( $exp[count($exp) - 2]!="model"){
            $namespace = $exp[count($exp) - 2];
        }else{
            $namespace = "";
        }

        return isset($this->modelToTable[$namespace."\\".$class]) ? $this->modelToTable[$namespace."\\".$class] : "";
    }

    function getModelForTable($table)
    {
        return isset($this->tableToModel[$table])?$this->tableToModel[$table]:"";
    }
}
?>