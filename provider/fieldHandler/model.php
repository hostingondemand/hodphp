<?php
namespace hodphp\provider\fieldHandler;

use hodphp\lib\model\BaseFieldHandler;

class Model extends BaseFieldHandler
{
    private $_class;
    private $_namespace;
    private $_data;

    function fromAnnotation($parameters, $type, $field)
    {
        $this->_class = $parameters[0];
        $this->_namespace = @$parameters[1] ?: false;
    }

    function model($class, $namespace = false)
    {
        $this->_class = $class;
        $this->_namespace = $namespace;
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
        if (!$this->_namespace) {
            $model = $this->model->{$this->_class};
        } else {
            $model = $this->model->{$this->_namespace}->{$this->_class};
        }

        if (is_object($value)) {
            $this->_data = $value;
        } else if (is_array($value)) {
            $this->_data = $model->fromArray($value);
        } else if (method_exists($model, 'initialize')) {
            $this->_data = $model->initialize($value);
        } else {
            $this->_data = null;
        }

        $this->_model->_setInData($this->_inModelName, $this->_data);
    }

}
