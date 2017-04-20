<?php

namespace hodphp\provider\serializer;

//simple php serializer
use hodphp\lib\serialization\Serializer;

class Php extends Serializer
{
    function serialize($data)
    {
        $data = $this->prepareObject($data);
        return serialize($data["data"]);
    }

    function unserialize($data, $assoc = false, $type = null)
    {
        return unserialize($data);
    }

}

?>