<?php
namespace framework\provider\fieldHandler;

use framework\lib\model\BaseFieldHandler;

class SerializedModel extends BaseFieldHandler
{
    private $_data;
    private $loaded=false;

    private $_class;
    private $_namespace;

    function fromAnnotation($parameters, $type, $field)
    {
        $this->_class = $parameters[0];
        $this->_namespace = @$parameters[1] ?: false;
    }


    function get($inModel)
    {
        if (empty($this->_data)) {
            return null;
        }

        return $this->_data;
    }

    function set($value)
    {
        if (is_string($value)) {
            $data = @unserialize($value);
            if($data!==false) {
                $this->_data = $this->toModel($data);
                $this->_model->_setInData($this->_inModelName, $value);
            }else{
                $data=@json_decode($value,true);
                if($data) {
                    $this->_data = $this->toModel($data);
                    $this->_model->_setInData($this->_inModelName, serialize($data));
                }else{
                    $this->_data = $value;
                    $this->_model->_setInData($this->_inModelName, serialize($value));
                }
            }
        }else{
            $this->_data =  $this->toModel($value);
            $this->_model->_setInData($this->_inModelName, serialize($value));
        }
    }

    function toModel($value){
        if (!$this->_namespace) {
            $model = $this->model->{$this->_class};
        } else {
            $model = $this->model->{$this->_namespace}->{$this->_class};
        }

        if(is_object($value)){
            return $value;
        }
        elseif (is_array($value)) {
           return $model->fromArray($value);
        } else if (method_exists($model, 'initialize')) {
            $model->initialize($value);
            return $model;
        } else {
            return null;
        }
    }

}
