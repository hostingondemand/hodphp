<?php
namespace lib;
use core\Lib;
use lib\service\BaseService;
//this is a simple service to write some config to the harddrive
class Config extends Lib
{

    var $data;
    var $invalididated;



    function get($key,$section="global",$default=""){
        if(!isset($this->data[$section])){
            $this->data[$section]=$this->filesystem->getArray("project/config/".$section.".php");
        }



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
            foreach($this->data as $section=>$data){
                $serialized=  "<?php return ".var_export($data, true);
                $this->filesystem->clearWrite("project/config/".$section.".php",$serialized);
                $this->invalididated=false;
            }
        }
    }

    function __destruct()
    {
        //save everything on destruct
        $this->save();
    }

}