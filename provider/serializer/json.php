<?php

namespace provider\serializer;
//simple json serializer
use lib\serialization\Serializer;

class Json extends Serializer
{
    function serialize($data, $type = null){
        return json_encode($data,JSON_PRETTY_PRINT);
    }
    function unserialize($data, $assoc = false, $type = null){
        $result = json_decode($data,$assoc);
        return $result;
    }

}

?>