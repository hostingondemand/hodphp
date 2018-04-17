<?php

namespace hodphp\lib;

use hodphp\core\Lib;

class Cache extends Lib
{

    var $projectSize = 0;
    var $cacheRoute;

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

    function pageCacheRecordStart($route,$settings,$user)
    {
        ob_start();
        $route=$this->getCorrectRoute($route,$settings);

        $file = 'data/cache/pageCache_' . md5($user."_".print_r($route, true)) . '.php';
        if($this->filesystem->exists($file)) {
            $result = $this->filesystem->getArray($file);
            $result["locked"]=true;
           $this->filesystem->writeArray($file,$result);
        }

    }

    function pageCacheRecordSave($route,$settings, $user = false)
    {
        $result = ob_get_contents();


        $validUntil=0;
        if($settings["ttl"]) {
            $validUntil = time() + ($settings["ttl"] * 60);
        }

        $route=$this->getCorrectRoute($route,$settings);

        if($settings["schedule"]){
            $now=time();
            $bestTime=$this->helper->schedule->nextUpdate($settings["schedule"],$now);
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
            'locked'=>false
        ];
        $this->debug->info("saved cache" ,["route"=>$route,"settings"=>$settings,"user"=>$user], "cache");
        $this->filesystem->writeArray('data/cache/pageCache_' . md5($user."_".print_r($route, true)) . '.php', $data);
    }

    function pageCacheGetPage($route,$settings,$user)
    {
        $route=$this->getCorrectRoute($route,$settings);

        $file = 'data/cache/pageCache_' . md5($user."_".print_r($route, true)) . '.php';
        $result = $this->filesystem->getArray($file);

        echo $result['output'];
    }

    function pageCacheNeedRefresh($route, $settings, $user=false)
    {
        $route=$this->getCorrectRoute($route,$settings);

        $file = 'data/cache/pageCache_' . md5($user."_".print_r($route, true)) . '.php';
        $result = $this->filesystem->getArray($file);
        if (!$this->filesystem->exists($file) || ($result['validUntil'] < time() && !$result["locked"])) {
            return true;
        }

        return false;
    }

    function getCorrectRoute($route,$settings){
        if(@$settings["useFullRoute"]){
            $fullRoute=$this->route->getRoute();
            if(!empty($fullRoute)) {
                $route = $fullRoute;
            }elseif($this->cache->cacheRoute){
                $route= $this->cache->cacheRoute;
            }
        }
        return $route;
    }
}