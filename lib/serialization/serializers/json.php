<?php

namespace lib\Serialization\serializers;
//simple json serializer
class Json extends \core\Lib
{

    function serialize($data){
        return json_encode($data,JSON_PRETTY_PRINT);
    }
    function unserialize($data,$assoc=false){
        return json_decode($data,$assoc);
    }

}

?>