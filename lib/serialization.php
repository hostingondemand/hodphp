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
}
