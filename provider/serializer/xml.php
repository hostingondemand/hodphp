<?php

namespace provider\serializer;
//simple json serializer
use lib\serialization\Serializer;

class XML extends Serializer
{
    function serialize($data, $type=null, $original=null){
        $initData=$this->getInitData($type, $data, $original);
        $xmlData = new \SimpleXMLElement($initData["wrapper"]);
        if($initData["rootElement"]){
            $this->arrayToXml($data[$initData["rootElement"]], $xmlData, $original->{$initData["rootElement"]});
        }else {
            $this->arrayToXml($data, $xmlData, $original);
        }
        return $xmlData->asXML();
    }

    function unserialize($data, $assoc=false, $type=null){
        if(!is_null($type)) {
            $annotData = $this->annotation->getAnnotationsForClass($type, 'serializeNamespace');

            if(!empty($annotData)) {
                $annotData = $this->annotation->translate($annotData[0]);
                return json_decode(json_encode(simplexml_load_string($data)->children($annotData->parameters[0], $annotData->parameters[1])), true);
            } else {
                $namespaces = simplexml_load_string($data)->getNamespaces();

                if(count($namespaces)) {
                    return json_decode(json_encode(simplexml_load_string($data)->children(array_keys($namespaces)[0], true)), true);
                }
            }
        }
        return json_decode(json_encode(simplexml_load_string($data)->children()), true);
    }

    function getInitData($type = null,$data=null,$original=null) {
        $customWrapper=null;
        $rootElement=false;
        $wrapper = '<?xml version="1.0" encoding="UTF-8"?>';

        if(!is_null($type)) {
            $annotData = $this->annotation->getAnnotationsForClass($type, 'serializeWrapper');

            if(!empty($annotData)) {
                $annotData = $this->annotation->translate($annotData[0]);
                $customWrapper = '<' . $annotData->parameters[0] . '></' . $annotData->parameters[0] . '>';
            }else{
                foreach($data as $key=>$val){
                    $annotData = $this->annotation->getAnnotationsForField($original,$key, 'serializeRoot');
                    if(!empty($annotData)){
                        $customWrapper= '<'.$key.'></'.$key.'>';
                        $rootElement=$key;
                        break;
                    }
                }
            }
        }

        if($customWrapper===null){
            $customWrapper="<data></data>";
        }

        return array("wrapper"=>$wrapper.$customWrapper,"rootElement"=>$rootElement);
    }

    function arrayToXml($data, &$xml_data,$original) {
        foreach($data as $key => $value) {
            if(is_array($original)&&is_object($original[$key])){
                $value=$original[$key];
            }

            if(is_object($original)) {
                $value = $original->$key;
            }

            if(is_object($value)) {
                $annotData = $this->annotation->getAnnotationsForClass($value->_getType(), 'serializeWrapper');
                if(!empty($annotData)) {
                    $annotData = $this->annotation->translate($annotData[0]);
                    $key=$annotData->parameters[0];
                }
            }

            if(is_numeric($key)) {
                $key = 'KeyMissing' . $key;
            }

            if(is_object($value)){
                $child = $xml_data->addChild($key);
                $this->arrayToXml($value->toArray(), $child,$value);
            } elseif (is_array($value)) {
                $child = $xml_data->addChild($key);
                $this->arrayToXml($value, $child,$value);
            } else {
                $xml_data->addChild($key, htmlspecialchars($value));
            }
        }
    }
}

?>