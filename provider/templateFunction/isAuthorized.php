<?php
namespace framework\provider\templateFunction;

class IsAuthorized extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = array(), $module = false)
    {
        return $this->auth->isAuthorized("view", $parameters[0], isset($parameters[1]) ? $parameters[1] : 1);
    }

}

