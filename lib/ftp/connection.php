<?php
namespace framework\lib\ftp;
class Connection
{
    private $con;

    function connect($url, $username, $password = "")
    {
        $this->con = ftp_connect($url);
        if (ftp_login($this->con, $username, $password)) {
            $this->debug->info("Connection succeded:", array("url",$url,"user"=>$username),"ftp");
            return $this;
        }
        $this->debug->error("Connection failed:", array("url",$url,"user"=>$username),"ftp");
        return false;
    }

    function __call($name, $arguments)
    {
        $this->debug->info("Ran command:", array("name",$name,"data"=>$arguments),"ftp");
        return call_user_func_array("ftp_" . $name, array_merge(array($this->con), $arguments));
    }

}