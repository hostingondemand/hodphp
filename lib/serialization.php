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
        $eventData["data"] = $this->LoadSerializer($format)->serialize($data);
        return $eventData["data"];
    }

    private function LoadSerializer($name)
    {
        return $this->provider->serializer->$name;
    }

    function unserialize($format, $data, $assoc = false, $type = null)
    {
        return $this->LoadSerializer($format)->unserialize($data, $assoc, $type);
    }

}
