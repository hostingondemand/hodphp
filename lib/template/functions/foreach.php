<?php
namespace lib\template\functions;

class FuncForeach extends \lib\template\AbstractFunction
{
    var $requireContent = true;
//loops through an array and interpretes the inside for every item in the array
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        $result = "";

        //first check if the given variable is an array in the first place
        if (is_array($parameters[0])) {

            //loop through the items
            foreach ($parameters[0] as $key => $val) {

                //set the variable names
                if (isset($unparsed[1])) {
                    $data[$unparsed[1]["parameters"][0]] = $val;
                }
                if (isset($unparsed[2])) {
                    $data[$unparsed[2]["parameters"][0]] = $key;
                }

                //and interpret the content of the loop
                $result .= $this->interpreter->interpret($content, $data);
            }
        }
        return $result;

    }
}

?>