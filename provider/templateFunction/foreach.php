<?php
namespace hodphp\provider\templateFunction;

class FuncForeach extends \hodphp\lib\template\AbstractFunction
{
    var $requireContent = true;

//loops through an array and interpretes the inside for every item in the array
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        $result = "";
        $arr = false;
        if (isset($parameters[0]) && is_object($parameters[0])) {
            $arr = $parameters[0]->getData();
        }

        //first check if the given variable is an array in the first place
        if (is_array($arr)) {

            //loop through the items
            foreach ($arr as $key => $val) {

                //set the variable names
                if (isset($unparsed[1])) {
                    $data->{$unparsed[1]["parameters"][0]} = $val;
                }
                if (isset($unparsed[2])) {
                    $data->{$unparsed[2]["parameters"][0]} = $key;
                }

                //and interpret the content of the loop
                $result .= $this->interpreter->interpret($content, $data);
            }
        }
        return $result;

    }
}

