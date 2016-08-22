<?php

namespace lib\Serialization\serializers;
//simple php serializer
class Php extends \core\Lib
{
    function serialize($data){
        return serialize($data);
    }
    function unserialize($data,$assoc=false){
        return unserialize($data);
    }

}

?>