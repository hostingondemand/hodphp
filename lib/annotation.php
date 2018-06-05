<?php
namespace framework\lib;

//a simple wrapper around the filesystem to be able to use files in the right directory
use framework\core\Loader;

class Annotation extends \framework\core\Lib
{

    var $found;

    function __construct()
    {
        Loader::loadClass("baseAspect", "lib/annotation");
    }

    function classHasAnnotations($class, $prefix = "")
    {
        return count($this->getAnnotationsForClass($class, $prefix));
    }


    function getAnnotationsForClass($class, $prefix = "")
    {

        if (!$class) {
            return [];
        }

        $key = md5("class" . $class . "_" . $prefix);
        if (!isset($this->found[$key])) {
            $r = new \ReflectionClass($class);
            $doc = $r->getDocComment();
            preg_match_all('#@' . $prefix . '(.*?)[\r]{0,1}\n#s', $doc, $annotations);
            if (isset($annotations[1])) {
                $this->found[$key] = $annotations[1];
            } else {
                $this->found[$key] = array();
            }
        }

        return $this->found[$key];
    }


    function getAnnotationsForMethod($class, $method, $prefix = "", $noClass = false)
    {
        $key = md5("method" . $class . "_" . $method . "_" . $prefix . "_" . $noClass);
        if (!isset($this->found[$key])) {
            try {
                $r = new \ReflectionMethod($class, $method);
                $doc = $r->getDocComment();
                preg_match_all('#@' . $prefix . '(.*?)[\r]{0,1}\n#s', $doc, $annotations);
                if (isset($annotations[1])) {
                    if ($noClass) {
                        $this->found[$key] = $annotations[1];
                    } else {
                        $this->found[$key] = array_merge($this->getAnnotationsForClass($class, $prefix), $annotations[1]);
                    }
                } else {
                    if ($noClass) {
                        $this->found[$key] = array();
                    } else {
                        $this->found[$key] = $this->getAnnotationsForClass($class, $prefix);
                    }
                }
            } catch (\Exception $ex) {
                if ($noClass) {
                    $this->found[$key] = array();
                } else {
                    $this->found[$key] = $this->getAnnotationsForClass($class, $prefix);
                }
            }

        }
        return $this->found[$key];
    }

    function getAnnotationsForField($class, $field, $prefix = "", $noClass = false)
    {

        $key = md5("field" . $class . "_" . $field . "_" . $prefix . "_" . $noClass);
        if (!isset($this->found[$key])) {

            try {
                $r = new \ReflectionProperty($class, $field);
                $doc = $r->getDocComment();
                preg_match_all('#@' . $prefix . '(.*?)[\r]{0,1}\n#s', $doc, $annotations);
                if (isset($annotations[1])) {
                    if ($noClass) {
                        $this->found[$key] = $annotations[1];
                    } else {
                        $this->found[$key] = array_merge($this->getAnnotationsForClass($class, $prefix), $annotations[1]);
                    }
                } else {
                    if ($noClass) {
                        $this->found[$key] = array();
                    } else {
                        $this->found[$key] = $this->getAnnotationsForClass($class, $prefix);
                    }
                }
            } catch (\Exception $ex) {
                if ($noClass) {
                    $this->found[$key] = array();
                } else {
                    $this->found[$key] = $this->getAnnotationsForClass($class, $prefix);
                }
            }
        }
        return $this->found[$key];

    }

    function getFieldsWithAnnotation($class, $annotation)
    {
        $annotationKey = md5("fieldsearch" . $class . "_" . $annotation);
        if (!isset($this->found[$annotationKey])) {
            $result=[];
            $vars = get_class_vars($class);
            foreach ($vars as $key => $var) {
                if ($this->fieldHasAnnotations($class, $key, $annotation)) {
                    $result[] = $key;
                }
            }
            $this->found[$annotationKey]=$result;
        }
        return $this->found[$annotationKey];
    }


    function methodHasAnnotations($class, $method, $prefix = "")
    {
        return isset($this->getAnnotationsForMethod($class, $method, $prefix)[0]);
    }

    function fieldHasAnnotations($class, $field, $prefix = "")
    {
        return isset($this->getAnnotationsForField($class, $field, $prefix)[0]);
    }

    function translate($annotation)
    {
        $annotationKey = md5("translation" . $annotation);
        if (!isset($this->found[$annotationKey])) {
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
            $this->found[$annotationKey] = (object)$result;
        }
        return $this->found[$annotationKey];
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


