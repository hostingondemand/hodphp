<?php

namespace provider\serializer;
//dummy serializer in case its just a string. Is used for auto content selection
use lib\serialization\Serializer;

class Text extends Serializer
{
    function serialize($data){
        $data=$this->prepareObject($data);
        return $data["data"];
    }
    function unserialize($data, $assoc = false, $type = null){
        return $data;
    }

}

?>