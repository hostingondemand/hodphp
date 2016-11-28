<?php

namespace provider\serializer;
//simple json serializer
class Form extends \core\Lib
{

    function serialize($data){
        return http_build_query($data);
    }
    function unserialize($data,$assoc=false){
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