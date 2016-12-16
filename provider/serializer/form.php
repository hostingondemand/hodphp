<?php

namespace provider\serializer;
//simple json serializer
use lib\serialization\Serializer;

class Form extends Serializer
{

    function serialize($data, $type = null,$original=null){
        return http_build_query($data);
    }
    function unserialize($data, $assoc = false, $type = null){
        $result=array();
        parse_str($data,$result);
        if($assoc){
            return (array)$result;
        }else{
            return (object)$result;
        }

    }

}

?>