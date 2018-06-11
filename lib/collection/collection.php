<?php
namespace framework\lib\collection;

//this class is made to handle optional parameters
class Collection extends \framework\core\Lib implements \Iterator, \arrayAccess, \countable
{
    private $data = [];
    private $position = 0;

    function setData($data)
    {
        $this->data = $data;
        return $this;
    }


    function where($lambda, $onlyValues = true)
    {
        $this->data = array_filter($this->data, $this->getCallable($lambda));
        if ($onlyValues) {
            $this->data = array_values($this->data);
        }
        return $this;
    }

    function select($lambda)
    {
        $this->data = array_map($this->getCallable($lambda), $this->data);
        return $this;
    }

    function orderBy($lamda, $order = "asc")
    {
        $data = $this->data;
        uasort($data, function ($a, $b) {
            $a = $this->execute($lambda, $a);
            $b = $this->execute($lambda, $b);
            if ($order == "asc") {
                return strnatcmp($a, $b);
            }
            return strnatcmp($b, $a);
        });
        $this->data = $data;
        return $this;
    }

    function toArray()
    {
        return $this->data;
    }

    function first()
    {
        return $this->data[0];
    }

    function last()
    {
        return $this->data[count($this->data) - 1];
    }

    public function execute($input, $params = [])
    {
        $callable = $this->getCallable($input);
        return call_user_func_array($callable, $params);
    }

    public function getCallable($input)
    {
        if (is_callable($input)) {
            return $input;
        }

        static $callables = [];

        if (!$callables[$input]) {
            if (is_string($input)) {
                $split = explode("=>", $input, 2);
                $result = eval('return function(' . $split[0] . '){return ' . $split[1] . ';};');
            }
            if (!is_callable($result)) {
                $result = function () {
                };
            }
            $callables[$input] = $result;
        }
        return $callables[$input];
    }


    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->data[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->data[$this->position]);
    }


    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }


    function count(){
        return count($this->data);
    }

}