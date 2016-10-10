<?php
namespace lib;

//a simple wrapper around the filesystem to be able to use files in the right directory
use core\Loader;

class Annotation extends \core\Lib
{

    function getAnnotationsForClass($class)
    {
        $r = new \ReflectionClass($class);
        $doc = $r->getDocComment();
        preg_match_all('#@(.*?)\n#s', $doc, $annotations);
        if (isset($annotations[1])) {
            return $annotations[1];
        }
        return array();
    }


    function getAnnotationsForMethod($class, $method)
    {

        $r = new \ReflectionMethod($class, $method);
        $doc = $r->getDocComment();
        preg_match_all('#@(.*?)\n#s', $doc, $annotations);
        if (isset($annotations[1])) {
            return array_merge($this->getAnnotationsForClass($class), $annotations[1]);
        }
        return array();
    }


    function getAnnotationsForField($class, $field)
    {

        $r = new \ReflectionProperty($class, $field);
        $doc = $r->getDocComment();
        preg_match_all('#@(.*?)\n#s', $doc, $annotations);
        if (isset($annotations[1])) {
            return array_merge($this->getAnnotationsForClass($class), $annotations[1]);
        }
        return array();
    }
}

?>