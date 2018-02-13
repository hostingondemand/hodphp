<?php
namespace hodphp\lib;

use hodphp\core\Loader;

//simple wrapper around the serializers
//so serialization can be done by just giving a serializername and the data
class Serialization extends \hodphp\core\Lib
{
    function __construct()
    {
        Loader::loadClass("serializer", "lib\\serialization");
    }

    function serialize($format, $data)
    {
        $serializer=$this->LoadSerializer($format);

        if($serializer) {
            $eventData["data"] = $serializer->serialize($data);
            return $eventData["data"];
        }else{
            $this->debug->error("Couldnt find serializer for format:". $format);
            return false;
        }

    }

    private function LoadSerializer($name)
    {
        return $this->provider->serializer->$name;
    }

    function unserialize($format, $data, $assoc = false, $type = null)
    {
        $serializer=$this->LoadSerializer($format);
        if($serializer) {
            return $serializer->unserialize($data, $assoc, $type);
        }else{
            $this->debug->error("Couldnt find serializer for format:". $format);
            return false;
        }
    }

}
