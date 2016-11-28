<?php
namespace provider\templateFunction;

class FuncInputFor extends \lib\template\AbstractFunction
{

    //make a text lowercase
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        $input = array(
            "name" => $parameters[0]);

        if(@$data->validationResult->errors->{$parameters[0]}){
            $input["invalid"]= true;
        }else{
            $input["invalid"]=false;
        }
        if (isset($parameters[1])) {
            $input["type"]=$parameters[1];
        }else{
            $input["type"]="string";
        }

        if(@$data->{$parameters[0]}){
            $input["value"]=$data->{$parameters[0]};
        }else{
            $input["value"]="";
        }
        $attributes="";
        if(isset($parameters[2])){
            $array=json_decode(str_replace("'",'"',$parameters[2]),true);

            if($input["invalid"]){
                if(isset($array["class"])){
                    $array["class"].=" invalid";
                }else{
                    $array["class"]="invalid";
                }
            }


            foreach($array as $attribute=>$value){
                $attributes.= $attribute.'="'.$value.'"';
            }
        }

        if(isset($parameters[3])){
            $input["source"]=$parameters[3];
        }

        $input["attributes"]=$attributes;



        return $this->template->parseFile("editorTemplates/".$input["type"], $input,"editorTemplates/string");
    }
}

?>