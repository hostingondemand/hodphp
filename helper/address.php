<?php
namespace hodphp\helper;
use hodphp\lib\helper\BaseHelper;

class Address extends BaseHelper
{
    function splitAddress($addr)
    {
        $exp = explode(" ", $addr);
        $cnt = count($exp);
        $pos = $cnt - 1;
        for ($i = $cnt - 1; $i > 0; $i-- ) {
            if (is_numeric(substr($exp[$i],0,1))) {
                $pos = $i;
                break;
            }

        }
        $street = "";
        for ($i = 0; $i < $pos; $i++){
            if ($i > 0) {
                $street .= " ";
            }
            $street.=$exp[$i];
        }

        $tmpnumber=$exp[$pos];
        $leng=strlen($tmpnumber);
        $foundLetter=false;
        $suffix = "";
        $number="";
        for($j=0;$j<$leng;$j++){
            $char= substr($tmpnumber,$j,1);
            if(!is_numeric($char)){
                $foundLetter=true;
            }
            if($foundLetter){
                $suffix.=$char;
            }else{
                $number.=$char;
            }
        }
        $pos++;

        if($pos<$cnt) {

            for ($i = $pos; $i < $cnt; $i++) {
                if ($i > $pos || $suffix) {
                    $suffix .= " ";
                }
                $suffix .= $exp[$i];
            }
        }

        return (object)array(
            "suffix"=>$suffix,
            "number" => $number,
            "street" => $street
        );
    }
}

?>
