<?php
namespace framework\lib;

use framework\core\Lib;
use framework\core\Loader;

//the template handler
class template extends Lib
{
    var $globals;
    var $globalModules = array();

    var $_inlineMode=false;

    function __construct()
    {
        $this->globals["path"] = $this->path->getHttp();
        $this->globals["isAuthenticated"] = $this->auth->isAuthenticated();
        Loader::LoadClass("AbstractFunction", "lib/template");
    }

    //parse and interpret a template
    function parse($template, $data = Array())
    {
        $data = $this->dataHandler($data);
        $data->add($this->globals);

        //get the parser
        $parser = Loader::getSingleton("globalParser", "lib\\template");
        $parsed = $parser->parse($template);

        //interpret the result of the parser
        $interpreter = Loader::getSingleton("interpreter", "lib\\template");

        //return the interpreted result
        return $interpreter->interpret($parsed, $data);
    }

    function inlineMode($function){
        $oldInlineMode=$this->_inlineMode;
        $this->_inlineMode=true;
            return $function();
        $this->_inlineMode=$oldInlineMode;
    }

    function isInlineMode(){
        return $this->_inlineMode;
    }
    //use parse to parse the content of a file

    function dataHandler($data)
    {
        $result = Loader::createInstance("dataHandler", "lib/template");
        $result->add($data);
        return $result;
    }

    function parseFile($file, $data = array(), $fallback = false)
    {

        $data = $this->dataHandler($data);
        $data->add($this->globals);
        //get the parser

        $parsed = $this->cache->runCached("template",
            array("unique" => md5($this->filesystem->findRightPath("template/" . $file . ".tpl")), "file" => $file, "fallback" => $fallback),
            $this->filesystem->getModified("template/" . $file . ".tpl"),
            function ($data) {
                $content = $this->filesystem->getFile("template/" . $data["file"] . ".tpl");
                if (!$content && $data["fallback"]) {
                    $content = $this->filesystem->getFile("template/" . $data["fallback"] . ".tpl");
                }
                //get the parser
                $parser = Loader::getSingleton("globalParser", "lib\\template");
                $parsed = $parser->parse($content);
                $this->debug->info("Parsed file",array("file",$data["file"]),"template");
                return $parsed;
            }
        );

        //interpret the result of the parser
        $interpreter = Loader::getSingleton("interpreter", "lib\\template");

        //return the interpreted result
        return $interpreter->interpret($parsed, $data);
    }

    function getModule($name)
    {
        Loader::loadClass("baseModule", "lib\\template");
        $instance = Loader::createInstance("module", "templateModule\\" . $name);
        if ($instance) {
            $instance->_name = $name;
            return $instance;
        }
        return false;
    }

    function registerGlobal($key, $val)
    {
        $this->globals[$key] = $val;
    }

    function registerGlobalModule($module)
    {
        if ($module) {
            $renames = $this->config->get("module.rename", "template");
            $this->globalModules[$module->_name] = $module;
            if (isset($renames[$module->_name])) {
                $this->globalModules[$renames[$module->_name]] = $module;
            }
        }

    }

    function getActiveModule($name)
    {
        if (isset($this->globalModules[$name])) {
            return $this->globalModules[$name];
        }
        return false;
    }

    function createDataSource($data, $value, $text,$optional=false)
    {
        if($optional){
            array_unshift($data,array($value=>"",$text=>""));
        }
        return array(
            "data" => $data,
            "value" => $value,
            "text" => $text
        );
    }

}


