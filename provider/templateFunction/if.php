<?php
namespace hodphp\provider\templateFunction;

class FuncIf extends \hodphp\lib\template\AbstractFunction
{
    var $requireContent = true;

    //this function handles an if
    function call($parameters, $data, $content = "", $unparsed = array(), $module = false)
    {
        //first split up the data into if, elseif and else
        $splitted = $this->splitData($content);

        //loop through this block
        foreach ($splitted as $key => $val) {

            if ($key == 0) {
                //if the condition of the first parameter is met.. interpret the content.
                if ($parameters[0]) {
                    return $this->interpreter->interpret($val, $data);
                }
            } elseif (fmod($key, 2)) {
                //else check if there need to be done another if
                if ($val["function"] == "elseif") {
                    //if the conditions for the else if are met.. interpret the content
                    if ($this->interpreter->interpretElement($val["parameters"][0], $data)) {
                        return $this->interpreter->interpret($splitted[$key + 1], $data);
                    }

                    //if no conditions are met.. interperte the else section.
                } else {
                    return $this->interpreter->interpret($splitted[$key + 1], $data);
                }
            }
        }
    }

    //simply split up in blocks for if, elseif and else.
    function splitData($data)
    {
        $result = Array();
        $current = Array();
        foreach ($data as $val) {
            if (($val["type"] == "variable" and $val["parameters"][0] == "else") || ($val["type"] == "function" && $val["function"] == "elseif")) {
                $result[] = $current;
                $result[] = $val;
                $current = array();
            } else {
                $current[] = $val;
            }
        }
        if (count($current) > 0) {
            $result[] = $current;
        }

        return $result;

    }
}

?>