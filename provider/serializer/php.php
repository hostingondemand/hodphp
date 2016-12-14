<?php

namespace provider\serializer;
//simple php serializer
use lib\serialization\Serializer;

class Php extends Serializer
{
    function serialize($data, $type = null,$original=null){
        return serialize($data);
    }
    function unserialize($data, $assoc = false, $type = null){
        return unserialize($data);
    }

}

?>