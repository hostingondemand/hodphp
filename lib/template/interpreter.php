<?php
namespace lib\template;

use core\Lib;
use \core\loader;


//this class is there to inteprete the parsed code..
class Interpreter extends Lib
{


    //loop through all elements and interpret those
    function interpret($content, $data)
    {

        $result = "";
        foreach ($content as $val) {
            $result .= $this->interpretElement($val, $data);
        }
        return $result;
    }


    //check which type the element is and call the right function to interpret this.
    function interpretElement($element, $data)
    {

        if ($element["type"] == "content") {
            return $element["content"];
        } elseif ($element["type"] == "value") {
            return $this->cleanValue($element["parameters"][0]);
        } elseif ($element["type"] == "variable") {
            return $this->getValue($element["parameters"][0], $data);
        } elseif ($element["type"] == "array") {
            return $this->getArrayValue($element["parameters"], $data);
        } elseif ($element["type"] == "function") {
            return $this->callFunction($element, $data);
        } elseif ($element["type"] == "comparison_root") {
            return $this->handleMainComparison($element, $data);
        } elseif ($element["type"] == "comparison_sub") {
            return $this->handleSubComparison($element, $data);
        }

        return "";
    }

    //just plain text.. only remove uotes around it
    function cleanValue($value)
    {
        if (substr($value, 0, 1) == "'" || substr($value, 0, 1) == '"') {
            $value = substr($value, 1);
        }
        if (substr($value, -1, 1) == "'" || substr($value, -1, 1) == '"') {
            $value = substr($value, 0, -1);
        }

        return $value;
    }


    //handle a comparison
    function handleMainComparison($expression, $data)
    {

        //loop through all parameters to get all or sections
        foreach ($expression["parameters"][0] as $or) {
            $result = true;

            // loop through all sub parameters to get all and sections
            foreach ($or as $and) {

                //compare those elements. if comparison fails.. return false
                if (!$this->interpretElement($and, $data)) {
                    $result = false;
                    break;
                }
            }
            //if one of the or conditions is succeeded return true.
            if ($result) {
                return true;
            }
        }
        //if all or conditions are false then return false;
        return false;
    }


    ///just some ifs which handle sub comparisons..
    function handleSubComparison($expression, $data)
    {
        $operator = $expression["operator"];
        $left = $this->interpretElement($expression["left"], $data);
        $right = $this->interpretElement($expression["right"], $data);

        if ($operator == "=" || $operator == "==") {
            return $left == $right;
        } elseif ($operator == ">") {
            return $left > $right;
        } elseif ($operator == "<") {
            return $left < $right;
        } elseif ($operator == ">=") {
            return $left >= $right;
        } elseif ($operator == "<=") {
            return $left <= $right;
        } elseif ($operator == "!=") {
            return $left != $right;
        }
        return false;
    }

    //a simple function call
    function callFunction($expression, $data)
    {
        $function=$expression["function"];
        $exp=explode(".",$function);

        //interpret the parameters
        foreach ($expression["parameters"] as $parameter) {
            $parameters[] = $this->interpretElement($parameter, $data);
        }


        if(count($exp)>1) {
                $module=$this->template->getActiveModule($exp[0]);
                if($module) {
                    return $module->callFunction($exp[1],$parameters, $data, $expression["content"], $expression["parameters"]);
                }
        }else {

            //load the function class
            $function = Loader::getSingleton($expression["function"], "provider\\templateFunction", "func");

            //call the function
            return $function->call($parameters, $data, $expression["content"], $expression["parameters"]);
        }
    }


    //get a value
    function getValue($key, $data)
    {
        if (isset($data[$key])) {
            return $data[$key];
        }
    }

    //handle an array
    function getArrayValue($keys, $data)
    {
        //just loop through every dimension of the array.. ad take the right key from this dimension.
        foreach ($keys as $key) {
            if ($key == 0) {
                $result = $data[$keys[0]];
            } else {
                $keyname = $this->interpretElement($key, $data);
                $result = $result[$keyname];
            }
        }

        return $result;

    }





}

?>