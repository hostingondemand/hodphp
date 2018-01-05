<?php
namespace hodphp\provider\fieldHandler;

use hodphp\lib\model\BaseFieldHandler;

class DbReference extends BaseFieldHandler
{
    private $_field;
    private $_toTable;
    private $obj;
    private $loaded;
    private $_toModel;
    private $_toModelNamespace;
    private $_fromTable;
    private $_cascadeDelete;
    private $_cascadeSave;

    function fromAnnotation($parameters, $type, $field)
    {
        $mapping = $this->provider->mapping->default;
        if (isset($parameters["model"]) && $parameters["toTable"] ) {
            $this->toModel($parameters["model"])
                ->toTable($parameters["toTable"]);
        }elseif(isset($parameters["model"])){
            $this->toTable($mapping->getTableForClass($parameters["model"]))
                ->toModel($parameters["model"]);
        } else if (isset($parameters["toTable"])) {
            $this->toModel($mapping->getModelForTable($parameters["toTable"]))
                ->toTable($parameters["toTable"]);
        }

        if (isset($parameters["key"])) {
            $this->field($parameters["key"]);
        } else if (isset($parameters["model"])) {
            $this->field($this->_toTable . "_id");
        }

        if (isset($parameters["cascade"])) {
            if ($parameters["cascade"] == "all") {
                $this->cascadeAll();
            }
            if ($parameters["cascade"] == "delete") {
                $this->cascadeDelete();
            }
            if ($parameters["cascade"] == "save") {
                $this->cascadeSave();
            }
            if ($parameters["cascade"] == "reference") {
                $this->updateReference();
            }
        }

    }

    function toModel($model, $namespace = false)
    {
        if (!$namespace) {
            $model = str_replace("/", "\\", $model);
            $exp = explode("\\", $model);
            if (isset($exp[1])) {
                $this->_toModel = $exp[1];
                $this->_toModelNamespace = $exp[0];
            } else {
                $this->_toModel = $exp[0];
            }
        } else {
            $this->_toModel = $model;
            $this->_toModelNamespace = $namespace;
        }
        return $this;
    }

    function toTable($toTable)
    {
        $this->_toTable = $toTable;
        return $this;
    }

    function field($field)
    {
        $this->_field = $field;
        return $this;
    }

    function cascadeAll()
    {
        $this->_cascadeDelete = true;
        $this->_cascadeSave = true;
        return $this;
    }

    function cascadeDelete()
    {
        $this->_cascadeDelete = true;
        return $this;
    }

    function cascadeSave()
    {
        $this->_cascadeSave = true;
        return $this;
    }

    function updateReference()
    {
        $this->_updateReference = true;
        return $this;
    }

    function save()
    {
        if ($this->loaded) {
            if (!$this->_fromTable && $this->_model) {
                $mapping = $this->provider->mapping->default;
                $this->fromTable($mapping->getTableForClass($this->_model->_getType()));
            }

            if ($this->_updateReference && is_object($this->obj)) {
                $this->db->query(
                    "update `" . $this->_fromTable . "` set `" . $this->_field . "`='" . ($this->obj->id?:"0") . "' where id='" . $this->_model->id . "'"
                );
            }elseif(!is_object($this->obj)){
                $this->db->query(
                    "update `" . $this->_fromTable . "` set `" . $this->_field . "`='0' where id='" . $this->_model->id . "'"
                );
            }

            if ($this->_cascadeSave && is_object($this->obj)) {
                $thisGet = $this->get(false);
                $this->db->saveModel($thisGet, $this->_toTable);
                $this->db->query(
                    "update `" . $this->_fromTable . "` set `" . $this->_field . "`='" . ($this->obj->id?:"0") . "' where id='" . $this->_model->id . "'"
                );
            }elseif(!is_object($this->obj)){
                $this->db->query(
                    "update `" . $this->_fromTable . "` set `" . $this->_field . "`='0' where id='" . $this->_model->id . "'"
                );
            }
        }

    }

    function fromTable($toTable)
    {
        $this->_fromTable = $toTable;
        return $this;
    }

    function get($inModel)
    {
        if (!$this->loaded && !$this->obj) {
            if ($this->_field) {
                $field = $this->_field;
                $id = $this->_model->$field;
            } else {
                $id = $inModel;
            }
            $this->obj = $this->db->query("select * from `" . $this->_toTable . "` where id ='" . $id . "'")->fetchModel($this->_toModel, $this->_toModelNamespace);
            $this->loaded = true;
        }
        return $this->obj;
    }

    function unload(){
        $this->loaded=false;
        if($this->obj) {
            $this->obj->__unload();
        }

        $this->obj=null;
    }

    function set($obj)
    {
        if($obj===false){
            $this->loaded=true;
        }
        elseif(is_numeric($obj)){
            $field = $this->_field;
            $this->_model->$field=$obj;
            $this->loaded=false;
        }else {
            if (is_array($obj)) {
                $this->obj = $this->get(false);
                if (!$this->obj) {
                    if ($this->_toModelNamespace) {
                        $this->obj = $this->model->{$this->_toModelNamespace}->{$this->_toModel};
                    } else {
                        $this->obj = $this->model->{$this->_toModel};
                    }
                }
                $this->obj->fromArray($obj);
                $this->loaded = true;
            }
            if (is_object($obj)) {
                $this->obj = $obj;
                $this->loaded = true;
            }
        }
    }

}
