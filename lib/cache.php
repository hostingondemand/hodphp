<?php

namespace hodphp\lib;

use hodphp\core\Lib;

class Cache extends Lib
{

    var $projectSize = 0;
    var $cacheRoute;
    var $ready=false;

    function __construct()
    {
        $this->provider->cache->default->setup();
        $this->ready=true;
    }

    function runCachedProject($key, $input, $function)
    {
        if ($this->projectSize == 0) {
            $this->projectSize = $this->filesystem->codeSize("project");
        }

        $name =  $key . "_" . md5(print_r($input, true));

        $entry=$this->provider->cache->default->loadEntry($name);
        if (is_array($entry) && $entry["projectSize"] == $this->projectSize) {
            return $entry["content"];
        }


        if ($this->debug->getLevel() <= 2) {
            $this->debug->info("Rebuilt cache for", array("key" => $key, "data" => $input), "cache");
        }

        $result = $function($input);
        $entry["projectSize"] = $this->projectSize;
        $entry["content"] = $result;
        $this->provider->cache->default->saveEntry($name,$entry);
        return $result;
    }

    function runCached($key, $data, $minDate, $function)
    {
        $name= $key . "_" . md5(print_r($data, true));

        $entry=$this->provider->cache->default->loadEntry($name);
        if(is_array($entry)&&$entry["creationDate"]>$minDate){
            return $entry["content"];
        }

        if ($this->debug->getLevel() <= 2) {
            $this->debug->info("Rebuilt cache for", array("key" => $key, "data" => $input), "cache");
        }

        $result = $function($data);
        $entry["creationDate"] = time();
        $entry["content"] = $result;

        $this->provider->cache->default->saveEntry($name,$entry);
        return $result;
    }

    function destroy()
    {
        $this->debug->info("Destroyed cache", array("files" => "All"), "cache");
        $this->provider->cache->default->clear();
    }

    function pageCacheRecordStart($route,$settings,$user)
    {
        ob_start();
        $route=$this->getCorrectRoute($route,$settings);
        $name=md5($user."_".print_r($route, true));
        $entry=$this->provider->cache->default->loadEntry($name);
        $entry["locked"]=true;
        $this->provider->cache->default->saveEntry($name,$entry);
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
            $bestTime=$this->helper->schedule->nextUpdate($now);
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
        $name="pageCache_".md5($user."_".print_r($route, true));
        $this->provider->cache->default->saveEntry($name,$data);
    }

    function pageCacheGetPage($route,$settings,$user)
    {
        $route=$this->getCorrectRoute($route,$settings);

        $name = 'pageCache_' . md5($user."_".print_r($route, true));
        $result = $this->provider->cache->default->loadEntry($name);

        echo $result['output'];
    }

    function pageCacheNeedRefresh($route, $settings, $user=false)
    {
        $route=$this->getCorrectRoute($route,$settings);

        $name=md5($user."_".print_r($route, true));
        $entry=$this->provider->cache->default->loadEntry($name);
        if (!$entry || ($entry['validUntil'] < time() && !$entry["locked"])) {
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