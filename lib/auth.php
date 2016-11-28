<?php
namespace lib;
use core\Lib;
use lib\service\BaseService;
//this is a simple service to write some config to the harddrive
class Auth extends Lib
{
    function isAuthenticated(){
        return true;
    }

    function isAuthorized($type,$key,$minLevel){
        return true;
    }

}