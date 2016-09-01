<?php
namespace lib\template\functions;

class FuncInputFor extends \lib\template\AbstractFunction
{

    //make a text lowercase
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        $input = array(
            "name" => $parameters[0]);

        if(isset($data["validationResult"]["errors"][$parameters[0]])){
            $input["invalid"]= true;
        }else{
            $input["invalid"]=false;
        }
        if (isset($parameters[1])) {
            $input["type"]=$parameters[1];
        }else{
            $input["type"]="string";
        }

        if(isset($data[$parameters[0]])){
            $input["value"]=$data[$parameters[0]];
        }else{
            $input["value"]="";
        }

        return $this->template->parseFile("editorTemplates/".$input["type"], $input,"editorTemplates/string");
    }
}

?>