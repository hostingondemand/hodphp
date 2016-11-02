<?php
namespace  lib\model;
use core\Base;


abstract class BaseModel extends Base
{

    private $_data = array();
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
        $this->_validationResult = $this->validation->validator("model")->validate($this);
    }

    function setupFieldHandlers()
    {
        $this->_fieldHandlers = $this->__fieldHandlers();
        if (is_array($this->_fieldHandlers)) {
            foreach ($this->_fieldHandlers as $fieldName=>$handler) {
                $handler->init($this,$fieldName);
            }
        } else {
            $this->_fieldHandlers = array();
        }
    }

    function __get($name)
    {


        $backtrace = debug_backtrace();
        $class = get_class($this);
        if (!($backtrace[1]["class"] == $class && $backtrace[1]["function"] == "get" . ucfirst($name)) && method_exists($this, "get" . ucfirst($name))) {
            $funcName = "get" . ucfirst($name);
            return $this->$funcName();

        } elseif (isset($this->_fieldHandlers[$name])) {
            return $this->_fieldHandlers[$name]->get(isset($this->_data[$name]) ? $this->_data[$name] : false);
        } elseif (isset($this->_data[$name])) {
            return $this->_data[$name];
        } else {
            return parent::__get($name);
        }

    }

    function __set($name, $value)
    {

        $backtrace = debug_backtrace();
        $class = get_class($this);
        if ($backtrace[1]["class"] != $class && method_exists($this, "set" . ucfirst($name))) {
            $funcName = "set" . ucfirst($name);
            $this->$funcName($value);
        } elseif (isset($this->_fieldHandlers[$name])) {
            $this->_fieldHandlers[$name]->set($value);
        } else {
            $this->_data[$name] = $value;
        }
        $this->_invalidated = true;

    }

    function __debugInfo()
    {
        return $this->_data;
    }


    function fromArray($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if ($val !== null) {
                    $this->__set($key, $val);
                }
            }
        }
        return $this;
    }

    function fromRequest()
    {
        $result = $this->fromArray($this->request->getData(true));
        if (is_array($this->request->getData(true))) {
            $validator = $this->__validator();
            $validationResult = $validator->validate($this);
            $result->_validationResult = $validationResult->toArray();
        }


        return $result;
    }


    function toArray()
    {
        $result = array();
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

    function _saved()
    {
        foreach ($this->_fieldHandlers as $handler) {
            $handler->save();
        }
        $this->_invalidated = false;
    }


    function _deleted()
    {
        foreach ($this->_fieldHandlers as $handler) {
            $handler->delete();
        }
        $this->_invalidated = false;
    }

    function __validator()
    {
        return $this->_validationResult = $this->validation->validator("model");
    }

    function __fieldHandlers()
    {
        return array();
    }

    function isValid()
    {
        return $this->_validationResult["success"];
    }

    function getValidationResult(){
        return $this->_validationResult;
    }

    function _setInData($key,$value){
        $this->_data[$key]=$value;
    }

    function _getFromData($key){
        if(isset($this->_data[$key])){
            return $this->_data[$key];
        }
        return false;
    }

    function _getData(){
        return $this->_data;
    }

}

?>
