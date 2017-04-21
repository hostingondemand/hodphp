<?php

namespace hodphp\provider\serializer;

//dummy serializer in case its just a string. Is used for auto content selection
use hodphp\lib\serialization\Serializer;

class Text extends Serializer
{
    function serialize($data)
    {
        $data = $this->prepareObject($data);
        return $data["data"];
    }

    function unserialize($data, $assoc = false, $type = null)
    {
        return $data;
    }

}

