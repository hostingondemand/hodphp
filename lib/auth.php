<?php
namespace hodphp\lib;

use hodphp\core\Lib;

//this is a simple service to write some config to the harddrive
class Auth extends Lib
{
    var $id=0;

    function setup($userId=false){

    }

    function isAuthenticated()
    {
        return true;
    }

    function isAuthorized($type, $key, $minLevel)
    {
        return true;
    }

    function  getUserName(){
        return "guest";
    }

    function getUserId(){
        return $this->id;
    }

    function setupFakeSession($id){
        $this->id=$id;
    }

    function isInFakeSession(){
        return $this->id!=0;
    }

    function stopFakeSession(){
       $this->id=0;
    }



}