<?php

namespace provider\serializer;
//simple json serializer
use lib\serialization\Serializer;

class XML extends Serializer
{
    function serialize($data, $type=null){
        $xmlData = new \SimpleXMLElement($this->pickWrapper($type));
        $this->array_to_xml($data, $xmlData);
        return $xmlData->asXML();
    }

    function unserialize($data, $assoc=false, $type=null){
        if(!is_null($type)) {
            $annotData = $this->annotation->getAnnotationsForClass($type, 'serializeNamespace');
            if(!empty($annotData)) {
                $annotData = $this->annotation->translate($annotData[0]);
                return json_decode(json_encode(simplexml_load_string($data)->children($annotData->parameters[0], $annotData->parameters[1])), true);
            }
        }
        return json_decode(json_encode(simplexml_load_string($data)->children()), true);
    }

    function pickWrapper($type = null) {
        $wrapper = '<?xml version="1.0" ?>';
        if(!is_null($type)) {
            $annotData = $this->annotation->getAnnotationsForClass($type, 'serializeWrapper');

            if(!empty($annotData)) {
                $annotData = $this->annotation->translate($annotData[0]);
                $wrapper .= '<' . $annotData->parameters[0] . '></' . $annotData->parameters[0] . '>';
                return $wrapper;
            }
        }
        return $wrapper . '<Data></Data>';
    }

    function array_to_xml($data, &$xml_data) {
        foreach($data as $key => $value) {
            if(is_numeric($key)) {
                $key = 'KeyMissing' . $key;
            }
            if (is_array($value)) {
                $child = $xml_data->addChild($key);
                $this->array_to_xml($value, $child);
            } else {
                $xml_data->addChild($key, htmlspecialchars($value));
            }
        }
    }
}

?>