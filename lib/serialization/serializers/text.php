<?php

namespace lib\Serialization\serializers;
//dummy serializer in case its just a string. Is used for auto content selection
class Text extends \core\Lib
{
    function serialize($data){
        return $data;
    }
    function unserialize($data,$assoc=false){
        return $data;
    }

}

?>