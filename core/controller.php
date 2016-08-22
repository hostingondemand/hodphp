<?php
namespace core;
//just a wrapper for controller.. might be useful someday
class Controller extends Base
{
    function authorize(){
        if($this->config->get("access.deny.".Loader::$module)){
            return false;
        }
        return true;
    }

    function onAuthorizationFail(){
        throw new \Exception("Authorization failed");
    }


}

?>