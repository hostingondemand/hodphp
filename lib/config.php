<?php
namespace lib;
use core\Lib;
use lib\service\BaseService;
//this is a simple service to write some config to the harddrive
class Config extends Lib
{

    var $data;
    var $invalididated;


    //load the config files initially
    function __construct()
    {


            if ($this->filesystem->exists("config.php")) {
                $this->data = include "config.php";
            }

            $this->invalididated=false;
    }



    function get($key,$default="",$section="global"){
        if(isset($this->data[$section][$key])){
            return $this->data[$section][$key];
        }

        return $default;
    }


    //set a variable
    function set($key,$val,$section="global"){
        $this->data[$section][$key]=$val;
        $this->invalidated=true;
    }


    //remove a variable
    function delete($key,$section="global"){
        unset($this->data[$section][$key]);
        $this->invalidated=true;
    }

    function save(){

            if($this->invalidated) {
                $serialized=  "<?php return ".var_export($this->data, true);
                $this->filesystem->clearWrite("config.php",$serialized);
                $this->invalididated=false;
            }

    }

    function __destruct()
    {
        //save everything on destruct
        $this->save();
    }

}