<?php
namespace framework\lib\model;

use framework\core\Base;

abstract class BaseModel extends Base
{

    var $__requiredFieldsCache = false;
    private $_data = [];
    private $_invalidated = false;
    private $_fieldHandlers;
    private $_validationResult;

    function __construct()
    {
        $vars = get_object_vars($this);
        foreach (get_object_vars($this) as $name => $value) {
            if (substr($name, 0, 1) != "_") {
                unset($this->$name);
                $this->_data[$name] = $value;
            }
        }

        $this->setupFieldHandlers();
        $this->_validationResult = ["success" => true, "errors" => []];
    }

    function setupFieldHandlers()
    {
        $this->_fieldHandlers = $this->__fieldHandlers();
        if (is_array($this->_fieldHandlers)) {
            foreach ($this->_fieldHandlers as $fieldName => $field) {
                if(is_object($field)){
                    $field=[$field];
                    $this->_fieldHandlers[$fieldName]=$field;
                }
                foreach($field as $handler) {
                    $handler->init($this, $fieldName);
                }
            }
        } else {
            $this->_fieldHandlers = [];
        }
    }

    static $annotatedFieldHandlers = [];

    function __fieldHandlers()
    {
        static $modelLib = false;
        static $annotationLib = false;
        if (!$modelLib || !$annotationLib) {
            $modelLib = $this->model;
            $annotationLib = $this->annotation;
        }


        $type = $this->_getType();
        if (!isset(self::$annotatedFieldHandlers[$type])) {
            self::$annotatedFieldHandlers[$type] = array();
            $type = $this->_getType();
            $vars = get_class_vars($type);
            foreach ($vars as $var => $val) {
                $annotations = $annotationLib->getAnnotationsForField($type, $var, "handle");
                foreach ($annotations as $annotation) {
                    $annotation = $annotationLib->translate($annotation);
                    self::$annotatedFieldHandlers[$type][] = ["annotation" => $annotation, "var" => $var];
                }
            }
        }

        $result = [];
        foreach (self::$annotatedFieldHandlers[$type] as $handlerInfo) {
            $handler = $modelLib->fieldHandler($handlerInfo["annotation"]->function);
            if ($handler) {
                $handler->fromAnnotation($handlerInfo["annotation"]->parameters, $type, $handlerInfo["var"]);
                $result[$handlerInfo["var"]][] = $handler;
            }
        }
        return $result;
    }

    function __get($name)
    {

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3);
        $class = get_class($this);
        if (!($backtrace[1]["class"] == $class && $backtrace[1]["function"] == "get" . ucfirst($name)) && method_exists($this, "get" . ucfirst($name))) {
            $this->goMyModule();
            $funcName = "get" . ucfirst($name);
            $this->_debugIn("Dynamic get", $name);
            $result = $this->$funcName();
            $this->_debugOut();
            $this->goBackModule();
            return $result;
        } elseif (isset($this->_fieldHandlers[$name][0])) {
            $this->goMyModule();
            $this->_debugIn("Fieldhandler get", $name);
            $result = isset($this->_data[$name]) ? $this->_data[$name] : false;
            foreach ($this->_fieldHandlers[$name] as $handler) {
                $result = $handler->get($result);
            }
            $this->_debugOut();
            $this->goBackModule();
            return $result;
        } elseif (isset($this->_data[$name])) {
            return $this->_data[$name];
        } else if (property_exists($class, $name)) {
            return null;
        } else {
            return parent::__get($name);
        }
    }

    function __set($name, $value)
    {

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3);
        $class = get_class($this);
        if ($backtrace[1]["class"] != $class && method_exists($this, "set" . ucfirst($name))) {
            $funcName = "set" . ucfirst($name);
            $this->_debugIn("Dynamic set", $name);
            $this->$funcName($value);
            $this->_debugOut();
        } elseif (isset($this->_fieldHandlers[$name][0])) {
            $this->_debugIn("Fieldhandler set", $name);


            foreach ($this->_fieldHandlers[$name] as $handler) {
                $handler->set($value);
                $value = $handler->get($value);
            }

            $this->_debugOut();
        } else {
            $this->_data[$name] = $value;
        }
        $this->_invalidated = true;
    }

    function __debugInfo()
    {
        return $this->_data;
    }

    function fromRequest()
    {
        $result = $this->fromArray($this->request->getData(true, $this->_getType()));
        if (is_array($this->request->getData(true))) {
            $validator = $this->__validator();
            $validationResult = $validator->validate($this);
            $result->_validationResult = $validationResult->toArray();
        }

        return $result;
    }

    function fromArray($data, $skipNull = false)
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if (!($skipNull && $val === null)) {
                    $this->__set($key, $val);
                }
            }
        }

        return $this;
    }

    function __validator()
    {
        $validator = $this->validation->validator("model");
        $type = $this->_getType();
        $vars = get_class_vars($type);
        foreach ($vars as $var => $val) {
            $annotations = $this->annotation->getAnnotationsForField($type, $var, "validate");
            foreach ($annotations as $annotation) {
                $annotation = $this->annotation->translate($annotation);
                $validator->add($var, $annotation->function, $annotation->parameters);
            }
        }

        return $validator;
    }

    function toArray()
    {
        $result = [];
        foreach ($this->_data as $key => $val) {
            if (is_array($this->_data[$key])) {
                foreach ($this->_data[$key] as $akey => $aval) {
                    if (substr($akey, 0, 1) != "_") {
                        if (is_object($this->_data[$key][$akey]) && method_exists($this->_data[$key][$akey], "toArray")) {
                            $result[$key][$akey] = $this->_data[$key][$akey]->toArray();
                        } else {
                            $result[$key][$akey] = $this->_data[$key][$akey];
                        }
                    }
                }
            } elseif (is_object($this->_data[$key]) && method_exists($this->_data[$key], "toArray")) {
                if (substr($key, 0, 1) != "_") {
                    $result[$key] = $this->_data[$key]->toArray();
                }
            } else {
                if (substr($key, 0, 1) != "_") {
                    $result[$key] = $this->_data[$key];
                }
            }
        }

        return $result;
    }

    function _isInvalidated()
    {
        return $this->_invalidated;
    }

    function _preSave()
    {
        foreach ($this->_fieldHandlers as $field) {
            foreach($field as $handler) {
                $handler->preSave();
            }
        }

    }


    function _saved()
    {
        foreach ($this->_fieldHandlers as $field) {
            foreach($field as $handler) {
                $handler->save();
            }
        }
        $this->_invalidated = false;
    }

    function _deleted()
    {
        foreach ($this->_fieldHandlers as $field) {
            foreach ($field as $handler) {
                $handler->delete();
            }
        }
        $this->_invalidated = false;
    }

    function isValid()
    {
        return $this->_validationResult["success"];
    }

    function getValidationResult()
    {
        return $this->_validationResult;
    }

    function _setInData($key, $value)
    {
        $this->_data[$key] = $value;
    }

    function _getFromData($key)
    {
        if (isset($this->_data[$key])) {
            return $this->_data[$key];
        }

        return false;
    }

    function _getData()
    {
        return $this->_data;
    }

    function isFieldRequired($field)
    {
        $required = $this->__requiredFields();
        if (@$required[$field]) {
            return true;
        }

        return false;
    }

    function __requiredFields()
    {
        if (!$this->__requiredFieldsCache) {
            $type = $this->_getType();
            $vars = get_class_vars($type);
            $required = [];
            foreach ($vars as $var => $val) {
                $annotations = $this->annotation->getAnnotationsForField($type, $var, "validate");
                foreach ($annotations as $annotation) {
                    $annotation = $this->annotation->translate($annotation);
                    $validator = $this->validation->validator($annotation->function);
                    $required[$var] = $validator ? $validator->isRequired() : false;
                }
            }
            $this->__requiredFieldsCache = $required;
        }

        return $this->__requiredFieldsCache;
    }

    function __hash()
    {
        return md5(print_r($this->_data, true));
    }

    function __equals($obj)
    {
        return $this->__hash() == $obj->__hash();
    }

    function __unload($handler = false)
    {
        if ($handler) {
            $handlers = [$this->_fieldHandlers[$handler]];
        } else {
            $handlers = $this->_fieldHandlers;
        }

        foreach ($handlers as $handler) {
            $handler->unload();
        }
    }
}


