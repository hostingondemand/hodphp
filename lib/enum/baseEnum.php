<?php
namespace hodphp\lib\enum;
abstract class BaseEnum extends \hodphp\core\Lib
{
    var $__values;
    var $__names;
    var $__current = -1;

    function __construct($current = -1)
    {
        $vars = get_object_vars($this);

        foreach (get_object_vars($this) as $name => $value) {

            if (substr($name, 0, 1) != "_") {
                unset($this->$name);
                if ($value) {
                    $this->__names[$value] = $name;
                } else {
                    $this->__names[] = $name;
                }
            }
        }
        $this->__values = array_flip($this->__names);

        if ($current >= 0) {
            $this->__current = $current;

        }
    }

    function __toString()
    {
        if (($this->__current) >= 0) {
            return $this->__get("name");
        }
        return "";
    }

    function __get($name)
    {
        if (($this->__current) >= 0) {
            if ($name == "value") {
                return $this->__current;
            }
            if ($name == "name") {
                return $this->__names[$this->__current];
            }
        } else {
            $className = get_class($this);
            $instance = new $className($this->__values[$name]);
            return $instance;
        }
    }

    function byValue($value)
    {
        $className = get_class($this);
        $instance = new $className($value);
        return $instance;
    }
}

