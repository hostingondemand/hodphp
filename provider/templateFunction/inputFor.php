<?php
namespace framework\provider\templateFunction;

use framework\core\Loader;
use framework\lib\template\ExpressionParser;

class InputFor extends \framework\lib\template\AbstractFunction
{

    //make a text lowercase
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        if (@$data->validationResult->errors->{$parameters[0]}) {
            $input["invalid"] = true;
        } else {
            $input["invalid"] = false;
        }
        if (isset($parameters[1])) {
            $input["type"] = $parameters[1];
        } else {
            $input["type"] = "string";
        }

        $parserInput=str_replace("\\\"","\"",$parameters[0]);
        Loader::getInfo("expressionParser","lib/template");
        $expressionParser=new ExpressionParser($parserInput);
        $input["value"] =  $this->interpreter->interpretElement((array)$expressionParser,$data);

        if($expressionParser->type=="variable"){
            $input["name"]=$expressionParser->parameters[0];
        }elseif($expressionParser->type=="array"){
            $input["name"]="";
            foreach($expressionParser->parameters as $parameter){
                if(is_array($parameter)){
                    $input["name"].="[".  ($this->interpreter->interpretElement($parameter,$data)?:"0")."]";
                }else{
                    $input["name"].=$parameter;
                }
            }
        }

        $attributes = "";
        if (isset($parameters[2])) {
            $attributeData = json_decode(str_replace("'", '"', stripslashes($parameters[2])), true);

            if ($input["invalid"]) {
                if (isset($attributeData["class"])) {
                    $attributeData["class"] .= " invalid";
                } else {
                    $attributeData["class"] = "invalid";
                }
            }

            foreach ($attributeData as $attribute => $value) {
                $attributes .= $attribute . '="' . $value . '"';
            }
        }

        if (isset($parameters[3])) {
            $input["source"] = $parameters[3];
        }

        $input["attributes"] = $attributes;
        $input["attributeData"] = $attributeData;

        if(is_string($input["value"])) {
            $input["value"] = htmlspecialchars($input["value"]);
        }

        $input["name"]=htmlspecialchars($input["name"]);

        if(!isset($input[$input["name"]])){
            $input[$input["name"]]=$input["value"];
        }


        return $this->template->parseFile("editorTemplates/" . $input["type"], $input, "editorTemplates/string");
    }
}

