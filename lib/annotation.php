<?php
namespace lib;

//a simple wrapper around the filesystem to be able to use files in the right directory
use core\Loader;

class Annotation extends \core\Lib
{

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

        $r = new \ReflectionMethod($class, $method);
        $doc = $r->getDocComment();
        preg_match_all('#@'.$prefix.'(.*?)\n#s', $doc, $annotations);
        if (isset($annotations[1])) {
            return array_merge($this->getAnnotationsForClass($class), $annotations[1]);
        }
        return array();
    }

    function methodHasAnnotations($class, $method,$prefix=""){
        return count($this->getAnnotationsForMethod($class,$method,$prefix));
    }

    function getAnnotationsForField($class, $field,$prefix="")
    {

        $r = new \ReflectionProperty($class, $field);
        $doc = $r->getDocComment();
        preg_match_all('#@'.$prefix.'(.*?)\n#s', $doc, $annotations);
        if (isset($annotations[1])) {
            return array_merge($this->getAnnotationsForClass($class), $annotations[1]);
        }
        return array();
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
            $result["parameters"]=explode(",",$exp[1]);
        }else{
            $result["parameters"]=array();
        }
        return (object)$result;
    }
}

?>