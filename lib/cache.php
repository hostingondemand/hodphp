<?php

namespace hodphp\lib;

use hodphp\core\Lib;

class Cache extends Lib
{

    var $projectSize = 0;

    function __construct()
    {
        if (!$this->filesystem->exists("data/cache")) {
            $this->filesystem->mkDir("data/cache");
        }
    }

    function runCachedProject($key, $input, $function)
    {
        if ($this->projectSize == 0) {
            $this->projectSize = $this->filesystem->codeSize("project");
        }
        $filename = "data/cache/" . $key . "_" . md5(print_r($input, true)) . ".php";
        $data = array();
        if ($this->filesystem->exists($filename)) {
            $data = $this->filesystem->getArray($filename);
            if ($data["projectSize"] == $this->projectSize) {
                return $data["content"];
            }
        }

        if ($this->debug->getLevel() <= 2) {
            $this->debug->info("Rebuilt cache for", array("key" => $key, "data" => $input), "cache");
        }

        $result = $function($input);
        $data["projectSize"] = $this->projectSize;
        $data["content"] = $result;
        $this->filesystem->writeArray($filename, $data);
        return $result;
    }

    function runCached($key, $data, $minDate, $function)
    {
        $filename = "data/cache/" . $key . "_" . md5(print_r($data, true)) . ".php";
        if ($this->filesystem->exists($filename) && $this->filesystem->getModified($filename) > $minDate) {
            return $this->filesystem->getArray($filename);
        } else {
            $this->debug->info("Rebuilt cache for", array("key" => $key, "data" => $data), "cache");
            $result = $function($data);
            $this->filesystem->writeArray($filename, $result);
            return $result;
        }

    }

    function destroy()
    {
        $this->debug->info("Destroyed cache", array("files" => "All"), "cache");

        $this->filesystem->rm("data/cache");
        $this->filesystem->mkdir("data/cache");
    }

    function pageCacheRecordStart()
    {
        ob_start();
    }

    function pageCacheRecordSave($route,$settings, $user = false)
    {
        $result = ob_get_contents();


        $validUntil=0;
        if($settings["ttl"]) {
            $validUntil = time() + ($settings["ttl"] * 60);
        }

        if($settings["schedule"]){
            $times=explode(" ",$settings["schedule"]);
            $now=time();

            $lowestTime=0;
            $bestTime=0;
            foreach($times as $time){
                $time=trim($time);
                $exp=explode(":",$time);
                $hour = $exp[0];
                $minutes=$exp[1];
                $timeToday=mktime($hour,$minutes,0,date("m"),date("d"),date("Y"));
                if($timeToday<$now && ($timeToday<$lowestTime||$lowestTime==0)){
                    $lowestTime=$timeToday;
                }

                if($timeToday>$now && ($timeToday<$bestTime||$bestTime==0)){
                    $bestTime=$timeToday;
                }
            }

            if(!$bestTime){
                $bestTime=$lowestTime+86400;
            }
            if($bestTime<$validUntil ||$validUntil==0){
                $validUntil=$bestTime;
            }
        }

        $data = [
            'output' => $result,
            'route' => $route,
            'user'=>$user,
            'settings'=>$settings,
            'validUntil' => $validUntil,
        ];

        $this->filesystem->writeArray('data/cache/pageCache_' . md5($user."_".print_r($route, true)) . '.php', $data);
    }

    function pageCacheGetPage($route,$settings,$user)
    {
        $file = 'data/cache/pageCache_' . md5($user."_".print_r($route, true)) . '.php';
        $result = $this->filesystem->getArray($file);

        echo $result['output'];
    }

    function pageCacheNeedRefresh($route, $settings, $user=false)
    {
        $file = 'data/cache/pageCache_' . md5($user."_".print_r($route, true)) . '.php';
        $result = $this->filesystem->getArray($file);
        if ($result['validUntil'] < time() || !$this->filesystem->exists($file)) {
            return true;
        }

        return false;
    }
}