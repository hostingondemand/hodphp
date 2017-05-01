<?php
namespace hodphp\helper;
use hodphp\lib\helper\BaseHelper;

class DateTime extends BaseHelper
{
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