<?php

namespace framework\provider\templateFunction;

use framework\core\Loader;

class RenderHead extends \framework\lib\template\AbstractFunction
{

    //make a text lowercase
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {

        $inlineMode=$this->template->isInlineMode();
        $this->event->raise("headPreRender", func_get_args());
        $result = "";
        foreach ($this->document->getStylesheets() as $stylesheet) {
            if($inlineMode){
                $fileContent=$this->getContentFor($stylesheet);
                $result .= $this->template->parseFile("components/inlineStylesheet", array("content" => $fileContent)) . "\n";
            }else{
                $result .= $this->template->parseFile("components/stylesheet", array("stylesheet" => $stylesheet)) . "\n";
            }
        }

        $varContent = "";
        foreach ($this->document->getVars() as $key => $value) {
            $varContent .= $this->template->parseFile("components/var", array(
                "key" => $key,
                "value" => $value
            ));
        }
        $varContent .= $this->template->parseFile("components/var", array(
            "key" => "_hoddebugInitVars",
            "value" => json_encode($this->debug->getInitArray())
        ));

        if ($varContent) {
            $result .= $this->template->parseFile("components/inlineScript", array("content" => $varContent));
        }

        foreach ($this->document->getScripts() as $script) {
            if(!$inlineMode) {
                $result .= $this->template->parseFile("components/script", array("script" => $script)) . "\n";
            }else{
                $fileContent=$this->getContentFor($script);
                $result .= $this->template->parseFile("components/inlineScript", array("content" => $fileContent));
            }
        }

        if ($this->session->_debugMode) {
            $result .= $this->template->parseFile("components/script", array("script" => "js/hoddebug.js")) . "\n";
        }

        $this->event->raise("headPostRender", func_get_args());
        return $result;
    }

    function getContentFor($file){
        if($file["module"]){
            Loader::goModule($file["module"]);
            $fileContent=$this->filesystem->getFile("content/".$file["path"]);
            $fileContent=$this->template->parse($fileContent);
            Loader::goBackModule();
        }else{
            $fileContent=$this->filesystem->getFile("content/".$file["path"]);
            $fileContent=$this->template->parse($fileContent);
        }
        return $fileContent;
    }
}

