<?php
namespace hodphp\provider\templateFunction;

use hodphp\core\Loader;
use hodphp\lib\template\ExpressionParser;

class FuncInputFor extends \hodphp\lib\template\AbstractFunction
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
            $array = json_decode(str_replace("'", '"', $parameters[2]), true);

            if ($input["invalid"]) {
                if (isset($array["class"])) {
                    $array["class"] .= " invalid";
                } else {
                    $array["class"] = "invalid";
                }
            }

            foreach ($array as $attribute => $value) {
                $attributes .= $attribute . '="' . $value . '"';
            }
        }

        if (isset($parameters[3])) {
            $input["source"] = $parameters[3];
        }

        $input["attributes"] = $attributes;

        return $this->template->parseFile("editorTemplates/" . $input["type"], $input, "editorTemplates/string");
    }
}

