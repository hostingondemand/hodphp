<?php
namespace framework\helper;

use framework\lib\helper\BaseHelper;

class Schedule extends BaseHelper
{

    function nextUpdate($schedule, $lastDone = 0, $pattern = "time")
    {
        if (method_exists($this, "nextUpdate_" . $pattern)) {
            return call_user_func_array([$this, "nextUpdate_" . $pattern], func_get_args());
        }
        return time();
    }

    function nextUpdate_dayOfMonth($days, $lastDone)
    {
        if (!$lastDone) {
            $lastDone = mktime(0, 0, 0, date("m"), 1, date("Y"));
        }

        $days = explode(" ", $days);
        $bestTime = 0;
        foreach ($days as $day) {
            $dayTime = mktime(0, 0, 1, date("m"), $day, date("Y"));

            if ($dayTime >= $lastDone && ($dayTime < $bestTime || $bestTime == 0)){
                $bestTime = $dayTime;
            }
        }
        if(!$bestTime){
            $bestTime=mktime(0, 0, 1, date("m")+1, min($days), date("Y"));
        }

        return $bestTime;
    }

    function nextUpdate_time($schedule, $lastDone)
    {
        if (!$lastDone) {
            $lastDone = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        }

        $times = explode(" ", $schedule);
        $lowestTime = 0;
        $bestTime = 0;
        foreach ($times as $time) {
            $time = trim($time);
            $exp = explode(":", $time);
            $hour = $exp[0];
            $minutes = $exp[1];
            $timeToday = mktime($hour, $minutes, 0, date("m"), date("d"), date("Y"));

            if ($timeToday < $lastDone && ($timeToday < $lowestTime || $lowestTime == 0)) {
                $lowestTime = $timeToday;
            }

            if ($timeToday > $lastDone && ($timeToday < $bestTime || $bestTime == 0)) {
                $bestTime = $timeToday;
            }

        }

        if (!$bestTime) {
            $bestTime = $lowestTime + 86400;
        }

        return $bestTime;
    }

    function needUpdate($schedule, $lastDone, $pattern = "time")
    {
        $nextUpdate = $this->nextUpdate($schedule, $lastDone, $pattern);
        return $nextUpdate < time();
    }
}

?>
