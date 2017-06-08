<?php
namespace hodphp\helper;
use hodphp\lib\helper\BaseHelper;

class DateTime extends BaseHelper
{

    function addYear($date,$amount=1){
        $year=date("Y",$date);
        $year+=$amount;
        return mktime(date("H",$date),date("i",$date),date("s",$date),date("n",$date),date("j",$date),$year);
    }

    function addMonth($date,$amount=1){
        $month=date("j",$date);
        $month+=$amount;
        return mktime(date("H",$date),date("i",$date),date("s",$date),date("n",$date),$month ,date("Y",$date));
    }

    function formattedToTimestamp($date, $split = "-", $order = array(1, 0, 2))
    {
        $exp = explode($split, $date);
        return mktime(0, 0, 0, $exp[$order[0]], $exp[$order[1]], $exp[$order[2]]);
    }

    function jsonToTimestamp($date){
        return substr($date,6,-5);
    }
}
?>