<?php
namespace framework\provider\templateFunction;

class _For extends \framework\lib\template\AbstractFunction
{
    var $requireContent = true;

//loops through an array and interpretes the inside for every item in the array
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        $result="";
        if(isset($parameters[1])){
            $to=$parameters[1];
        }else{
            $to=0;
        }
        if(isset($parameters[2])){
            $from=$parameters[2];
        }else{
            $from=0;
        }
        if(isset($parameters[3])){
            $step=$parameters[3];
        }else{
            $step=1;
        }

        for($i=$from; $i<=$to;$i+=$step){
            $data->{$unparsed[0]["parameters"][0]} = $i;
            $result .= $this->interpreter->interpret($content, $data);
        }
        return $result;

    }
}

