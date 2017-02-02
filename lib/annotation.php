<?php
namespace lib;

//a simple wrapper around the filesystem to be able to use files in the right directory
use core\Loader;

class Annotation extends \core\Lib
{

    function __construct()
    {
        Loader::loadClass("baseAspect", "lib/annotation");
    }


    function getAnnotationsForClass($class, $prefix = "", $uncached = false)
    {
        if (!$uncached) {
            if(substr($class,0,1)!="\\"){
                $class="\\".$class;
            }
            $class=strtolower($class);
            $all=$this->getAllAnnotations();
            if(isset($all[$class]["class"])) {
                if (!$prefix) {
                    return $all[$class]["class"];
                } else {
                    $len=strlen($prefix);
                    $result=array();
                    foreach($all[$class]["class"] as $val){
                        if(substr($val,0,$len)==$prefix){
                            $result[]=substr($val,$len);
                        }
                    }
                    return $result;
                }
            }
            return array();
        }

        $r = new \ReflectionClass($class);
        $doc = $r->getDocComment();
        preg_match_all('#@' . $prefix . '(.*?)[\r]{0,1}\n#s', $doc, $annotations);
        if (isset($annotations[1])) {
            return $annotations[1];
        }
        return array();
    }

    function classHasAnnotations($class, $prefix = "")
    {
        return count($this->getAnnotationsForClass($class, $prefix));
    }


    function getAnnotationsForMethod($class, $method, $prefix = "", $uncached = false, $noClass = false)
    {
        if (!$uncached) {
            if(substr($class,0,1)!="\\"){
                $class="\\".$class;
            }
            $class=strtolower($class);
            $method=strtolower($method);
            $this->getAllAnnotations();
            $all=$this->getAllAnnotations();
            $result=array();
            if(isset($all[$class]["method"][$method])) {
                if (!$prefix) {
                    $result= $all[$class]["method"][$method];
                } else {
                    $len=strlen($prefix);
                    foreach($all[$class]["method"][$method] as $val){
                        if(substr($val,0,$len)==$prefix){
                            $result[]=substr($val,$len);
                        }
                    }
                }
            }
            if($noClass){
                return $result;
            }
            return array_merge($this->getAnnotationsForClass($class, $prefix, $uncached), $result);
        }
        try {
            $r = new \ReflectionMethod($class, $method);
            $doc = $r->getDocComment();
            preg_match_all('#@' . $prefix . '(.*?)[\r]{0,1}\n#s', $doc, $annotations);
            if (isset($annotations[1])) {
                if($noClass){
                    return $annotations[1];
                }
                return array_merge($this->getAnnotationsForClass($class, $prefix, $uncached), $annotations[1]);
            }
        } catch (\Exception $ex) {
        }
        if ($noClass) {
            return array();
        }
        return $this->getAnnotationsForClass($class, $prefix, $uncached);
    }

    function methodHasAnnotations($class, $method, $prefix = "")
    {
        return count($this->getAnnotationsForMethod($class, $method, $prefix));
    }

    function getAnnotationsForField($class, $field, $prefix = "", $uncached = false, $noClass = false)
    {
        if (!$uncached) {
            if(substr($class,0,1)!="\\"){
                $class="\\".$class;
            }
            $class=strtolower($class);
            $field=strtolower($field);
            $this->getAllAnnotations();
            $all=$this->getAllAnnotations();
            $result=array();
            if(isset($all[$class]["field"][$field])) {
                if (!$prefix) {
                    $result= $all[$class]["field"][$field];
                } else {
                    $len=strlen($prefix);
                    foreach($all[$class]["field"][$field] as $val){
                        if(substr($val,0,$len)==$prefix){
                            $result[]=substr($val,$len);
                        }
                    }
                }
            }
            if($noClass){
                return $result;
            }
            return array_merge($this->getAnnotationsForClass($class, $prefix, $uncached), $result);
        }

        try {
            $r = new \ReflectionProperty($class, $field);
            $doc = $r->getDocComment();
            preg_match_all('#@' . $prefix . '(.*?)[\r]{0,1}\n#s', $doc, $annotations);
            if (isset($annotations[1])) {
                if ($noClass) {
                    return $annotations[1];
                }
                return array_merge($this->getAnnotationsForClass($class, $prefix, $uncached), $annotations[1]);
            }
        } catch (\Exception $ex) {
        }
        if ($noClass) {
            return array();
        }
        return $this->getAnnotationsForClass($class, $prefix, $uncached);
    }

    function getAllAnnotations()
    {
        static $annotations = false;
        if (!$annotations) {
            $annotations = $this->cache->runCachedProject(
                "annotation_", array(),
                function () {
                    Loader::loadClass("baseModel", "lib/model");
                    Loader::loadClass("baseService", "lib/service");
                    $result = array();
                    $files = $this->filesystem->getFilesRecursive(array("project/controller", "project/service", "project/model", "project/modules", "modules"), "php");
                    foreach ($files as $file) {
                        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                            $useFile=str_replace("\\","/",$file);
                            $removePath=str_replace("\\","/",$this->path->getApp());
                        }else{
                            $useFile=$file;
                            $removePath=$this->path->getApp();
                        }
                        if (strpos($useFile, '/controller/') !== false || strpos($useFile, '/service/') !== false || strpos($useFile, '/model/') !== false) {
                            $type = str_replace($removePath, "", $useFile);
                            $type = str_replace(".php", "", $type);
                            $type=str_replace("/","\\",$type);
                            include_once($file);
                            if (class_exists($type)) {
                                $subResult = array();

                                //annoatations for class
                                try {
                                    $classAnnotations = $this->getAnnotationsForClass($type, "", true);
                                } catch (exception $ex) {
                                    $classAnnotations = array();
                                }
                                foreach ($classAnnotations as $annotation) {
                                    $subResult["class"][] = $annotation;
                                }


                                $methods = get_class_methods($type);
                                foreach ($methods as $method) {
                                    try {
                                        $methodAnnotations = $this->getAnnotationsForMethod($type, $method, "", true,true);
                                    } catch (exception $ex) {
                                        $methodAnnotations = array();
                                    }
                                    foreach ($methodAnnotations as $annotation) {
                                        $subResult["method"][strtolower($method)][] = $annotation;
                                    }
                                }

                                $fields = get_class_vars($type);
                                foreach ($fields as $field => $val) {
                                    try {
                                        $fieldAnnotations = $this->getAnnotationsForField($type, $field, "", true,true);
                                    } catch (exception $ex) {
                                        $fieldAnnotations = array();
                                    }
                                    foreach ($fieldAnnotations as $annotation) {
                                        $subResult["field"][strtolower($field)][] = $annotation;
                                    }
                                }
                                    $result[strtolower($type)] = $subResult;
                            }
                        }
                    }
                    return $result;
                }
            );
        }
        return $annotations;
    }


    function fieldHasAnnotations($class, $field, $prefix = "")
    {
        return count($this->getAnnotationsForField($class, $field, $prefix));
    }

    function translate($annotation)
    {
        $exp = explode("(", $annotation);
        $result["function"] = lcfirst($exp[0]);
        if (@$exp[1]) {
            if (substr($exp[1], -1) == ")") {
                $exp[1] = substr($exp[1], 0, -1);
            }
            $parameters = explode(",", $exp[1]);

            foreach ($parameters as $key => $val) {
                $exp = explode("=>", $val);
                if (count($exp) > 1) {
                    unset($parameters[$key]);
                    $parameters[$exp[0]] = $exp[1];
                }
            }
            $result["parameters"] = $parameters;


        } else {
            $result["parameters"] = array();
        }
        return (object)$result;
    }


    function runAspect($method, $aspects, $data)
    {
        foreach ($aspects as $aspect) {
            if (substr($aspect, -1)) {
                $aspect = substr($aspect, 0, -1);
            }

            $exp = explode("(", $aspect);
            $instance = Loader::getSingleton($exp[0], "aspect");
            if ($instance) {
                if (count($exp) > 1) {
                    $parameters = explode(",", $exp[1]);
                    foreach ($parameters as $key => $val) {
                        $exp = explode("=>", $val);
                        if (count($exp) > 1) {
                            unset($parameters[$key]);
                            $parameters[$exp[0]] = $exp[1];
                        }
                    }
                } else {
                    $parameters = array();
                }
                $instance->$method($parameters, $data);
            }
        }
    }
}



?>
