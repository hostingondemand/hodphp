<?php
namespace core;
class Base
{
    public function __get($name)
    {
        //dynamically load libraries
        return Loader::getSingleton($name, "lib");
    }
}

?>