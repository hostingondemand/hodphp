<?php
namespace lib;
use core\Lib;
use lib\service\BaseService;
//this is a simple service to write some config to the harddrive
class Auth extends Lib
{
    private $user;
    function __construct()
    {
        $this->user=$this->service->user->getUserByHash($this->session->userHash);
    }

    function isAuthenticated(){
        return true;
    }

    function isAuthorized($type,$key,$minLevel){
        return true;
    }

}