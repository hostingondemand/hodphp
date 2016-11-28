<?php
namespace lib;

use core\Loader;

//simple wrapper around the serializers
//so serialization can be done by just giving a serializername and the data
class Serialization extends \core\Lib
{
    function __construct()
    {
        Loader::loadClass("serializer", "lib\\serialization");
    }

    function serialize($format, $data)
    {
        $original = $data;
        if (is_array($data)) {
            $original = array();
            $type = array();
            foreach ($data as $key => $val) {
                if (is_object($data[$key]) && method_exists($data[$key], "toArray")) {
                    $original[$key] = $data[$key];
                    $type[$key] = $data[$key]->_getType();
                    $data[$key] = $data[$key]->toArray();
                } else {
                    if (is_array($val)) {
                        $type[$key] = "array";
                    } else {
                        $type[$key] = "value";
                    }
                    $original[$key]=$val;
                }

            }
        } else if (is_object($data) && method_exists($data, "toArray") && method_exists($data, "_getType")) {
            $type = $data->_getType();
            $data = $data->toArray();
        } else {
            $type = "value";
        }
        $eventData = array(
            "data" => $data,
            "type" => $type,
            "original" => $original,
        );
        $eventData=$this->preSerialize($eventData);
        $eventData = $this->event->raise("preSerialize", $eventData);
        $eventData["data"] = $this->LoadSerializer($format)->serialize($eventData["data"]);
        $eventData = $this->event->raise("postSerialize", $eventData);
        return $eventData["data"];
    }

    function unserialize($format, $data, $assoc = false)
    {
        return $this->LoadSerializer($format)->unserialize($data, $assoc);
    }


    private function LoadSerializer($name)
    {
        return $this->provider->serializer->$name;
    }



    function preSerialize($data)
    {
        $newData=$data["data"];
        if(is_array($data["type"])){
            foreach($data["data"] as $key=>$val){

                if($data["type"][$key]!="array" && $data["type"][$key]!="value") {
                    $_data=array(
                        "data"=>$data["data"][$key],
                        "type"=>$data["type"][$key],
                        "original"=>$data["original"][$key]
                    );
                    $_dataNew=$this->handle($_data);
                    $newData[$key]=$_dataNew["data"];
                }else{
                    $dataNew[$key]=$val;
                }
            }
        }
        if($data["type"]!="array" && $data["type"]!="value") {
            if(!is_array($data["type"])) {
                $vars = get_class_vars($data["type"]);

                foreach ($data["data"] as $key => $value) {
                    $annotations = $this->annotation->getAnnotationsForField($data["type"], $key, "serialize");
                    foreach ($annotations as $annotation) {
                        $annotation = $this->annotation->translate($annotation);
                        if ($annotation->function == "ignore") {
                            unset($newData[$key]);
                        }
                        if ($annotation->function == "rename") {
                            unset($newData[$key]);
                            $newData[$annotation->parameters[0]] = $data["data"][$key];
                        }
                        if ($annotation->function == "dynamic") {
                            $newData[$key] = $this->dynamicGet($data, $key);
                        }
                        if($annotation->function=="enumName"){
                            $newData[$key] =  $newData[$key] = $data["original"]->{$key}->name;
                        }
                        if($annotation->function=="modelOnly" && !property_exists(get_class($data["original"]),$key)){
                            unset($newData[$key]);
                        }
                    }
                }
            }
            $data["data"]=$newData;
        }
        return $data;
    }


    function dynamicGet($data,$key){
        $tempData=$data["original"]->$key;
        if(is_array($tempData)){
            foreach($tempData as $_key => $_value){
                if(is_object($_value)){
                    $_data=array(
                        "data"=>$_value->toArray(),
                        "type"=>$_value->_getType(),
                        "original"=>$_value
                    );
                    $_dataNew=$this->handle($_data);
                    $tempData[$_key]=$_dataNew["data"];
                }
            }
        }elseif(is_object($tempData)){
            $_data=array(
                "data"=>$tempData->toArray(),
                "type"=>$tempData->_getType(),
                "original"=>$tempData
            );
            $_dataNew=$this->handle($_data);
            $tempData=$_dataNew["data"];
        }

        return $tempData;
    }
}
