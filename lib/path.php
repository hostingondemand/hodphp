<?php
namespace framework\lib;
class Path extends \framework\core\Lib
{

    function getApp()
    {
        return substr(__DIR__, 0, -4);
    }

    function getHttp()
    {
        return $this->config->get("http.path", "server");
    }

}

