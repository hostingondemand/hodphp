<?php
namespace hodphp\helper;
use hodphp\lib\helper\BaseHelper;

class Address extends BaseHelper
{
    function splitAddress($addr){
        $exp=explode(" ",$addr);
        $cnt=count($exp);
        $number=$exp[$cnt-1];
        unset($exp[$cnt-1]);
        $street=implode(" ",$exp);
        return (object)array("number"=>$number,"street"=>$street);
    }
}

?>
