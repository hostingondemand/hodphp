<?php

namespace provider\serializer;
//simple json serializer
use lib\serialization\Serializer;

class Form extends Serializer
{

    function serialize($data){
        $data=$this->prepareObject($data);
        return http_build_query($data["data"]);
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