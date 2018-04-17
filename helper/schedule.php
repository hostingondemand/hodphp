<?php
namespace hodphp\helper;

use hodphp\lib\helper\BaseHelper;

class Schedule extends BaseHelper
{

    function nextUpdate($schedule,$lastDone=false){
        if(!$lastDone){
            $lastDone=mktime(0,0,0,date("m"),date("d"),date("Y"));
        }
        $times=explode(" ",$schedule);
        $lowestTime=0;
        $bestTime=0;
        foreach($times as $time){
            $time=trim($time);
            $exp=explode(":",$time);
            $hour = $exp[0];
            $minutes=$exp[1];
            $timeToday=mktime($hour,$minutes,0,date("m"),date("d"),date("Y"));

            if($timeToday<$lastDone && ($timeToday<$lowestTime||$lowestTime==0)){
                $lowestTime=$timeToday;
            }

            if($timeToday>$lastDone && ($timeToday<$bestTime||$bestTime==0)){
                $bestTime=$timeToday;
            }

        }

        if(!$bestTime){
            $bestTime=$lowestTime+86400;
        }

        return $bestTime;
    }

    function needUpdate($schedule, $lastDone)
    {
        $nextUpdate=$this->nextUpdate($schedule,$lastDone);
        return $nextUpdate<time();
    }
}

?>
