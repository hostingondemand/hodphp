<?php
namespace hodphp\provider\fieldHandler;

use hodphp\lib\model\BaseFieldHandler;

class ModelList extends BaseFieldHandler
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

    function set($values)
    {
        if (is_array($values)) {
            $result = array();
            foreach ($values as $value) {
                if(is_array($value) && isset($value[0])){
                    $this->set($value);
                    return false;
                } else {
                    if (!$this->_namespace) {
                        $model = $this->model->{$this->_class};
                    } else {
                        $model = $this->model->{$this->_namespace}->{$this->_class};
                    }

                    if (is_object($value)) {
                        $result[] = $value;
                    } else if (is_array($value)) {
                        $result[] = $model->fromArray($value);
                    } else if (method_exists($model, 'initialize')) {
                        $result[] = $model->initialize($value);
                    }
                }
            }
            $this->_data = $result;
        } else {
            $this->_data = array();
        }

        $this->_model->_setInData($this->_inModelName, $this->_data);
    }

}
