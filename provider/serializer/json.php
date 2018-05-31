<?php

namespace framework\provider\serializer;

//simple json serializer
use framework\lib\serialization\Serializer;

class Json extends Serializer
{
    function serialize($data)
    {
        $data = $this->prepareObject($data);
        return json_encode($data["data"], JSON_PRETTY_PRINT);
    }

    function unserialize($data, $assoc = false, $type = null)
    {
        $result = json_decode($data, $assoc);
        return $result;
    }

}

