<?php
namespace hodphp\provider\fieldHandler;

use hodphp\lib\model\BaseFieldHandler;


class DbManyToMany extends BaseFieldHandler
{
    private $_fromField;
    private $_toField;
    private $_glueTable;
    private $_toTable;
    private $obj;
    private $loaded;
    private $_toModel;
    private $_toModelNamespace;
    private $_cascadeSave;
    private $_updateList;
    private $_saveReset;
    private $_initArray;

    function fromAnnotation($parameters,$type,$field)
    {
        $mapping=$this->provider->mapping->default;
        if(isset($parameters["model"])){
            $this->toTable($mapping->getTableForClass($parameters["model"]))
                ->toModel($parameters["model"]);
        }else if(isset($parameters["toTable"])){
            $this->toModel($mapping->getModelForTable($parameters["toTable"]))
                ->toTable($parameters["toTable"]);
        }

        if(isset($parameters["toKey"])){
            $this->toField($parameters["toKey"]);
        }else if(isset($parameters["model"])){
            $this->toField($mapping->getTableForClass($parameters["model"])."_id");
        }
        if(isset($parameters["fromKey"])){
            $this->fromField($parameters["fromKey"]);
        }else{
            $this->fromField($mapping->getTableForClass($type)."_id");
        }

        if(isset($parameters["glueTable"])){
            $this->glueTable($parameters["glueTable"]);
        }

        if(isset($parameters["updateList"])){
            if($parameters["updateList"]=="all"){
                $this->updateList();
                $this->saveReset();
            }
            if($parameters["updateList"]=="insert"){
                $this->updateList();
            }
        }

        if(isset($parameters["cascade"])){
            if($parameters["cascade"]=="all"||$parameters["cascade"]=="save"){
                $this->cascadeSave();
            }
        }
    }

    function fromField($field)
    {
        $this->_fromField = $field;
        return $this;
    }

    function toField($field)
    {
        $this->_toField = $field;
        return $this;
    }

    function glueTable($glueTable)
    {
        $this->_glueTable = $glueTable;
        return $this;
    }

    function toTable($toTable)
    {
        $this->_toTable = $toTable;
        return $this;
    }

    function toModel($model, $namespace = false)
    {
        if(!$namespace){
            $model=str_replace("/","\\",$model);
            $exp=explode("\\",$model);

            if(isset($exp[1])){
                $this->_toModel=$exp[1];
                $this->_toModelNamespace=$exp[0];
            }else{
                $this->_toModel=$exp[0];
            }
        }else {
            $this->_toModel = $model;
            $this->_toModelnamespace= $namespace;
        }
        return $this;
    }

    function get($inModel)
    {
        if (!$this->loaded) {
            if ($this->_initArray) {

                $this->obj = array();
                foreach ($this->_initArray as $val) {
                    $model = $this->_toModel;
                    if ($this->_toModelNamespace) {
                        $namespace = $this->_toModelNamespace;
                        $this->obj[] = $this->model->$namespace->$model->fromArray($val);
                    } else {
                        $this->obj[] = $this->model->$model->fromArray($val);
                    }

                }
            } else {
                $this->obj = $this->db->query("select tt.* from `" . $this->_toTable . "` as tt
                left join `" . $this->_glueTable . "` as gt on gt.`" . $this->_toField . "`=tt.id
                where gt.`" . $this->_fromField . "` ='" . $this->_model->id . "'")
                    ->fetchAllModel($this->_toModel, $this->_toModelNamespace);
            }
            $this->loaded = true;
        }
        return $this->obj;
    }

    function set($value)
    {
        if (is_array($value)) {
            $this->_initArray = $value;
            $this->loaded=false;
        }
    }

    function save()
    {


        $originalData = $this->db->query("select tt.* from `" . $this->_toTable . "` as tt
                left join `" . $this->_glueTable . "` as gt on gt.`" . $this->_toField . "`=tt.id
                where gt.`" . $this->_fromField . "` ='" . $this->_model->id . "'")
            ->fetchAll();

        $originalData = $this->toIdMap($originalData);
        $data = $this->get(true);


        if ($this->_saveReset) {
            if ($this->db->parent) {
                $where = "parent_id='" . $this->db->parent["id"] . "' and parent_module='" . $this->db->parent["module"] . "' and  `" . $this->_fromField . "` ='" . $this->_model->id . "' ";
            } else {
                $where = "`" . $this->_fromField . "` ='" . $this->_model->id . "'";
            }
            $this->db->query("delete from " . $this->_glueTable . " where " . $where);

        }

        $idField = $this->_toField;
        foreach ($data as $val) {
            if ($this->_cascadeSave) {
                if (!$val->$idField) {
                    $val->$idField = $this->_model->id;
                }
                $saveResult = $this->db->saveModel($val, $this->_toTable);
                if (($saveResult["mode"] == "insert" || $this->_saveReset) && $this->_updateList) {
                    $query = "insert into " . $this->_glueTable . "
                        set `" . $this->_fromField . "` = '" . $this->_model->id . "',
                         `" . $this->_foField . "`='" . $saveResult["id"] . "'
                    ";

                    if ($this->db->parent) {
                        $query .= ",parent_id='" . $this->db->parent["id"] . "',
                         parent_module='" . $this->db->parent["module"] . "' ";
                    }


                    $this->db->query($query);
                }

                if (isset($originalData[$val->id])) {
                    unset($originalData[$val->id]);
                }
            } else if ($this->_updateList && (!isset($originalData[$val->id]) || $this->_saveReset)) {
                $query = "insert into " . $this->_glueTable . "
                        set `" . $this->_fromField . "` = '" . $this->_model->id . "',
                         `" . $this->_toField . "`='" . $val->id . "'
                    ";

                if ($this->db->parent) {
                    $query .= ",parent_id='" . $this->db->parent["id"] . "',
                         parent_module='" . $this->db->parent["module"] . "' ";
                }


                $this->db->query($query);

            }
        }


    }

    private function toIdMap($array)
    {
        $result = array();
        if ($array) {
            foreach ($array as $key => $val) {
                if (!is_array($val)) {
                    $val = $val->toArray();
                }
                $result[$val["id"]] = $val;
            }
        }
        return $result;
    }

    function cascadeAll()
    {
        $this->_cascadeSave = true;
        $this->_updateList = true;
        return $this;
    }

    function cascadeSave(){
        $this->_cascadeSave = true;
        return $this;
    }

    function updateList()
    {
        $this->_updateList = true;
        return $this;
    }

    function saveReset()
    {
        $this->_saveReset = true;
        return $this;
    }
}
