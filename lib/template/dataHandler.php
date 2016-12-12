<?php
namespace lib\template;

use core\Lib;

class DataHandler extends Lib
{
    var $__data = array();

    function add($data)
    {
        $this->__data[] = $data;
    }

    function addOnKey($key, $data)
    {
        $this->__data[$key] = $data;
    }

    function removeOnKey($key)
    {
        unset($this->__data[$key]);
    }

    function getData($key = 0)
    {
        $data = $this->__data[$key];
        //in case its nested by some internal construction
        if (is_object($data) && $data->_getType() == $this->_getType()) {
            return $data->getData();
        }
        return $data;
    }

    function __get($key)
    {
        $result = "";
        foreach ($this->__data as $data) {
            try {
                if (is_array($data)) {
                    $result = @$data[$key];
                }
                if (is_object($data)) {
                    $result = @$data->$key;
                }
            } catch (\Exception $ex) {
            }

            if ($result) {

                if (is_object($result) || is_array($result)) {
                    return \core\core()->template->dataHandler($result);
                }
                return $result;
            }

        }
        return "";
    }

    function set($key, $val = "")
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->__set($k, $v);
            }
        } else {
            $this->__set($key, $val);
        }
        return $this;
    }

    function __set($key, $val)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->__set($k, $v);
            }
        } else {
            if (!isset($this->__data["tpl"])) {
                $this->__data["tpl"] = array();
            }
            $this->__data["tpl"][$key] = $val;
        }
    }
}

?>