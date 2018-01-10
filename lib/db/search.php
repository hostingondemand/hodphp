<?php
namespace hodphp\lib\db;

use hodphp\core\Lib;
use hodphp\core\Loader;

class Search extends Lib
{
    var $query;
    var $keywords;
    var $_useScores=false;

    function initialize($keywords, $query)
    {
        $this->query=$query;
        $this->keywords=$this->generateKeywordArray($keywords);
        $this->fields=$this->generateFieldsArray();
    }

    function generateFieldsArray(){
        $modelInfo=$this->query->getModelInfo();
        $classInfo=Loader::getInfo($modelInfo["class"],"model".($modelInfo["namespace"]?"\\".$modelInfo["namespace"]:""));
        $class=$classInfo->type;

        $fields=$this->annotation->getFieldsWithAnnotation($class,"searchable");
        $fields=array_map(function($field) use ($class){
            $annotation=$this->annotation->getAnnotationsForField($class, $field, "searchable");
            $translation=$this->annotation->translate($annotation[0]);
            return [
                "name"=>$field,
                "score"=>$translation->parameters[0]?:1
            ];
        },$fields);

        return $fields;
    }


    function generateKeywordArray($keywords){
        $result=["required"=>[],"optional"=>[],"ignore"=>[]];
        $len=strlen($keywords);

        $escaping=true;
        $quoteCount=0;

        $buffer="";

        $required=false;
        $ignore=false;

        $wordPos=0;

        for($i=0;$i<$len;$i++){
            $letter=substr($keywords,$i,1);
            if($letter=="\\"){
                $escaping==true;
            }elseif($escaping){
                $buffer.=$letter;
                $wordPos++;
                $escaping=false;
            }else{

                if(fmod($quoteCount,2)){
                    if($letter=='"'){
                        $quoteCount++;
                    }else{
                        $buffer.=$letter;
                    }
                } elseif($letter==" " ){

                    if($buffer) {
                        $result[$required ? "required" : ($ignore ? "ignore" : "optional")][] = $buffer;
                    }
                    $wordPos=0;
                    $quoteCount=0;
                    $buffer="";
                    $required=false;
                    $ignore=false;
                }elseif($letter=="+" && !$wordPos){
                    $required=true;
                }elseif($letter=="-" && !$wordPos){
                    $ignore=true;
                }elseif($letter=='"') {
                    $quoteCount++;
                }else{
                    $buffer.=$letter;
                    $wordPos++;
                }

            }
        }

        if($buffer) {
            $result[$required ? "required" : ($ignore ? "ignore" : "optional")][] = $buffer;
        }

        return $result;
    }

    function useScores(){
        $this->_useScores=true;
        return $this;
    }



    function fetchAll(){
        return $this->provider->search->default->search($this->query,$this->keywords,$this->fields,$this->_useScores);
    }


    function fetchAllModel(){
        $items=$this->fetchAll();
        $result=[];
        $modelInfo=$this->query->getModelInfo();
        $modelClass=$modelInfo["class"];

        $modelBuilder=$this->model;
        if($modelInfo["namespace"]){
            $namespace=$modelInfo["namespace"];
            $modelBuilder=$modelBuilder->$namespace;
        }

        foreach($items as $item){
            $result[]=$modelBuilder->$modelClass->fromArray($item);
        }

        return $result;

    }


}

?>