<?php
namespace framework\lib;

use framework\core\Lib;
use framework\core\Loader;

//this is a simple service to write some config to the harddrive
class Collection extends Lib
{

    function createCollection(){
       return Loader::createInstance("collection","lib/collection");
    }

    function fromArray($data){
        $collection=$this->createCollection();
        $collection->setData($data);
        return $collection;
    }
}