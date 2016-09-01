<?php
namespace lib;

use \core\Loader;
use \core\Lib;


//the template handler
class template extends Lib
{
    var $globals;
    var $globalModules=array();

    function __construct()
    {
        $this->globals["path"]=$this->path->getHttp();
        $this->globals["isAuthenticated"]=$this->auth->isAuthenticated();
    }
    //parse and interpret a template
    function parse($template, $data = Array(),$modules=array())
    {
        $data=array_merge($data,$this->globals);
        $modules=array_merge($modules,$this->globalModules);
        //get the parser
        $parser = Loader::getSingleton("globalParser", "lib\\template");
        $parsed = $parser->parse($template,$modules);

        //interpret the result of the parser
        $interpreter = Loader::getSingleton("interpreter", "lib\\template");

        //return the interpreted result
        return $interpreter->interpret($parsed, $data,$modules);
    }

    //use parse to parse the content of a file
    function parseFile($file, $data = array(),$fallback=false, $modules=array())
    {
        $content = $this->filesystem->getFile("template/" . $file . ".tpl");
        if(!$content && $fallback){
            $content=$this->filesystem->getFile("template/" . $fallback . ".tpl");
        }
        return $this->parse($content, $data,$modules);
    }

    function getModule($name)
    {
        Loader::loadClass("baseModule","lib\\template");
        $instance= Loader::createInstance("module","templateModules\\".$name);
        $instance->_name=$name;
        return $instance;
    }

    function registerGlobal($key,$val){
        $this->globals[$key]=$val;
    }

    function registerGlobalModule($module){
        $this->globalModules[]=$module;
    }

}

?>