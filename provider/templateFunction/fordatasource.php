<?php
namespace hodphp\provider\templateFunction;

class FuncFordatasource extends \hodphp\lib\template\AbstractFunction
{
    var $requireContent = true;
//loops through an array and interpretes the inside for every item in the array
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {

        if(is_object($parameters[0])) {
            $datasource = $parameters[0]->getData();

            if (isset($parameters[1])) {
                if (is_object($parameters[1])) {
                    $dataSelected = $parameters[1]->getData();
                } else {
                    $dataSelected = $parameters[1];
                }
            } else {
                $dataSelected = "";
            }
            $dataSelected = $this->toSelectedMap($dataSelected, $datasource["value"]);


            $result = "";
            //first check if the given variable is an array in the first place
            if (is_array($datasource["data"])) {

                //loop through the items
                foreach ($datasource["data"] as $val) {
                    if (is_object($val)) {
                        $val = $val->toArray();
                    }
                    $data->_value = $val[$datasource["value"]];
                    $data->_text = $val[$datasource["text"]];
                    $data->_selected = isset($dataSelected[$val[$datasource["value"]]]) && $dataSelected[$val[$datasource["value"]]];
                    $result .= $this->interpreter->interpret($content, $this->template->dataHandler($data));
                }

            }

            return $result;
        }
        return "";

    }

    function toSelectedMap($dataset,$valueField){
        if(is_object($dataset)){
            $dataset=array($dataset->toArray());
        }
        else if(is_array($dataset) && isset($dataset[$valueField])){
            $dataset=array($dataset);
        }elseif(!is_array($dataset)){
            $dataset=array($dataset);
        }


        $result=array();
        foreach($dataset as $val){
            if(is_object($val)){
                $val=$val->$valueField;
            }else if(is_array($val)){
                $val=$val[$valueField];
            }
            $result[$val]=true;
        }

        return $result;


    }




}

?>