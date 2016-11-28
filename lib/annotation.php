<?php
namespace lib;

//a simple wrapper around the filesystem to be able to use files in the right directory
use core\Loader;

class Annotation extends \core\Lib
{

    function __construct()
    {
        Loader::loadClass("baseAspect","lib/annotation");
    }


    function getAnnotationsForClass($class,$prefix="")
    {
        $r = new \ReflectionClass($class);
        $doc = $r->getDocComment();
        preg_match_all('#@'.$prefix.'(.*?)\n#s', $doc, $annotations);
        if (isset($annotations[1])) {
            return $annotations[1];
        }
        return array();
    }

    function classHasAnnotations($class,$prefix=""){
        return count($this->getAnnotationsForClass($class,$prefix));
    }


    function getAnnotationsForMethod($class, $method,$prefix="")
    {

        try {
            $r = new \ReflectionMethod($class, $method);
            $doc = $r->getDocComment();
            preg_match_all('#@' . $prefix . '(.*?)\n#s', $doc, $annotations);
            if (isset($annotations[1])) {
                return array_merge($this->getAnnotationsForClass($class,$prefix), $annotations[1]);
            }
        }catch(\Exception $ex){}
        return array();
    }

    function methodHasAnnotations($class, $method,$prefix=""){
        return count($this->getAnnotationsForMethod($class,$method,$prefix));
    }

    function getAnnotationsForField($class, $field,$prefix="")
    {
        try {
        $r = new \ReflectionProperty($class, $field);
        $doc = $r->getDocComment();
        preg_match_all('#@' . $prefix . '(.*?)\n#s', $doc, $annotations);
        if (isset($annotations[1])) {
            return array_merge($this->getAnnotationsForClass($class,$prefix), $annotations[1]);
        }
        }catch(\Exception $ex){}
        return $this->getAnnotationsForClass($class,$prefix);
    }

    function fieldHasAnnotations($class, $field,$prefix=""){
        return count($this->getAnnotationsForField($class,$field,$prefix));
    }

    function translate($annotation){
        $exp = explode("(", $annotation);
        $result["function"]=lcfirst($exp[0]);
        if(@$exp[1]){
            if(substr($exp[1],-1)==")") {
                $exp[1]=substr($exp[1],0,-1);
            }
            $parameters=explode(",",$exp[1]);

            foreach($parameters as $key=>$val) {
                $exp = explode("=>", $val);
                if (count($exp) > 1) {
                    unset($parameters[$key]);
                    $parameters[$exp[0]] = $exp[1];
                }
            }
            $result["parameters"]=$parameters;


        }else{
            $result["parameters"]=array();
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
                } else {
                    $parameters = array();
                }
                $instance->$method($parameters, $data);
            }
        }
    }
}

?>
