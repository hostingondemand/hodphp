<?php
namespace hodphp\lib\ftp;
class Connection{
    private $con;
    function connect($url,$username,$password=""){
        $this->con=ftp_connect($url);
        if(ftp_login($this->con,$username,$password)){
            return $this;
        }
        return false;
    }

    function __call($name, $arguments)
    {
        return call_user_func_array("ftp_".$name,array_merge(array($this->con),$arguments));
    }

}