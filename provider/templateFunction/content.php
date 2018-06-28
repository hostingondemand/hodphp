<?php
namespace framework\provider\templateFunction;

use framework\core\Loader;

class Content extends \framework\lib\template\AbstractFunction
{
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        $inlineMode = $this->template->isInlineMode();
        foreach ($parameters as $key => $parameter) {
            if (is_object($parameter)) {
                $parameters[$key] = $parameter->getData();
            }
        }

        if (is_array($parameters[0]) && isset($parameters[0]["module"])) {
            $module = $parameters[0]["module"];
        } elseif (isset($parameters[1])) {
            $module = $parameters[1];
        } elseif (Loader::$module) {
            $module = Loader::$module;
        } else {
            $module = "";
        }

        if (is_array($parameters[0]) && isset($parameters[0]["path"])) {
            $path = $parameters[0]["path"];
        } elseif (!is_array($parameters[0])) {
            $path = $parameters[0];
        } else {
            $path = "";
        }

        if ($inlineMode) {
            $result= $this->getContentFor($path,$module);
            return $result;
        } else {

            if ($module) {
                $parameters = array_merge(array($module, "_files", "content"), array($path));
            } else {
                $parameters = array_merge(array("_files", "content"), array($path));
            }

            $oldAutoRoute = $this->route->autoRoute;
            $this->route->setAutoRoute(array());
            $route = $this->route->createRoute($parameters);
            $this->route->setAutoRoute($oldAutoRoute);
            return $route;
        }
    }



    function getContentFor($file,$module)
    {
        if ($module) {
            Loader::goModule($module);
            $loadedFile=$this->loadFile($file);
            Loader::goBackModule();
        } else {
            $loadedFile=$this->loadFile($file);
        }

        $result="data:".$loadedFile->type.";base64,".base64_encode($loadedFile->content);

        return $result;
    }


    function loadFile($file){
        $fileContent = $this->filesystem->getFile("content/" . $file);
        $fileContent = $this->template->parse($fileContent);
        $contentType=$this->filesystem->getContentType("content/" .$file);
        return (object)["type"=>$contentType,"content"=>$fileContent];
    }


}

