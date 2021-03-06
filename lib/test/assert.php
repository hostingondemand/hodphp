<?php
namespace framework\lib\test;
use framework\core\Base;

class assert extends Base
{
    var $results = array();

    var $title;
    var $function;
    var $inputData;

    function eq($expected)
    {
        $function = $this->function;
        $result = $function($this->inputData);
        $this->results[] =
            array(
                "title" => $this->title,
                "comparison" => $result . " == " . $expected,
                "success" => $expected == $result
            );

        return $this;
    }

    function nEq($expected)
    {
        $function = $this->function;
        $result = $function($this->inputData);
        $this->results[] =
            array(
                "title" => $this->title,
                "comparison" => $result . " != " . $expected,
                "success" => $expected != $result
            );

        return $this;
    }


    function gt($expected)
    {
        $function = $this->function;
        $result = $function($this->inputData);
        $this->results[] =
            array(
                "title" => $this->title,
                "comparison" => $result . " > " . $expected,
                "success" => $result > $expected
            );

        return $this;
    }

    function lt($expected)
    {
        $function = $this->function;
        $result = $function($this->inputData);
        $this->results[] =
            array(
                "title" => $this->title,
                "comparison" => $result . " < " . $expected,
                "success" => $result < $expected
            );

        return $this;
    }

    function gtEq($expected)
    {
        $function = $this->function;
        $result = $function($this->inputData);
        $this->results[] =
            array(
                "title" => $this->title,
                "comparison" => $result . " >= " . $expected,
                "success" => $result >= $expected
            );

        return $this;
    }

    function ltEq($expected)
    {
        $function = $this->function;
        $result = $function($this->inputData);
        $this->results[] =
            array(
                "title" => $this->title,
                "comparison" => $result . " <= " . $expected,
                "success" => $result <= $expected
            );

        return $this;
    }

    function method($instance, $method, $parameters = array())
    {
        $parametersStr = implode(",", $parameters);
        $title = $instance->_getType() . "->" . $method . "(" . $parametersStr . ")";
        return $this->anonymous($title, array($instance, $method, $parameters), function ($data) {
            $instance = $data[0];
            $method = $data[1];
            $parameters = $data[2];
            return call_user_func_array(array($instance, $method), $parameters);
        });
    }

    function anonymous($title, $data, $callback)
    {
        $this->initialize($title, $data, $callback);
        return $this;
    }

    function initialize($title, $data, $function)
    {
        $this->title = $title;
        $this->function = $function;
        $this->inputData = $data;
    }

    function field($instance, $field)
    {
        $title = $instance->_getType() . "->" . $field;
        return $this->anonymous($title, array($instance, $field), function ($data) {
            $instance = $data[0];
            $field = $data[1];
            return $instance->$field;
        });
    }

    function value($value,$name="")
    {
        return $this->anonymous($name?:$value, $value, function ($value) {
            return $value;
        });
    }

}

