<?php
namespace lib;
use core\Loader;

class Response extends \core\Lib
{

    var $partialMode = false;
    var $masterView = "main";
    var $contentTypes = [
        "json" => "application/json",
        "form" => "application/x-www-form-urlencoded",
        "xml" => "application/xml",
        "csv" => "text/csv"
    ];

    function write($string, $options = Array())
    {
        ob_clean();
        echo $string;
    }

    function renderAction($parameters = "")
    {


        if (func_num_args() > 1) {
            $parameters = func_get_args();
        }

        Loader::loadAction($parameters);
    }


    function renderView($data = Array(), $path = "")
    {

        if ($this->partialMode) {
            return $this->renderPartial($data, $path);
        }
        if (!is_array($data) && !is_object($data)) {
            $path = $data;
            $data = Array();
        }

        if (!$path) {
            $path = \core\Loader::$controller . "/" . (\core\Loader::$action);
        }

        $content = $this->template->parseFile($path, $data);
        $this->write($this->template->parseFile($this->masterView, Array("content" => $content)));
    }


    function renderContent($content)
    {
        $this->write($this->template->parseFile($this->masterView, Array("content" => $content)));
    }

    function renderPartial($data = Array(), $path = "")
    {
        if (!is_array($data) && !is_object($data)) {
            $path = $data;
            $data = Array();
        }

        if (!$path) {
            $path = \core\Loader::$controller . "/" . \core\Loader::$action;
        }

        $this->write($this->template->parseFile($path, $data));
    }

    function renderFile($data, $contentType)
    {
        $this->contentType($contentType);
        $this->write($data);
        die();
    }


    function renderJson($data)
    {
        $this->contentType("application/json");
        $this->write($this->serialization->serialize("json", $data));
    }


    function header($key, $value)
    {
        header($key . ": " . $value);
    }

    function contentType($type)
    {
        $this->header("content-type", $type);
    }

    function redirect()
    {
        $this->header("location", $this->route->createRoute(func_get_args()));

        die();
    }

    function redirectBack()
    {
        $this->header("location", $this->request->getReferer());
    }

    function fakeResponse($data, $function)
    {
        ob_start();
        $function($data);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    function setPartialMode($value)
    {
        $this->partialMode = $value;
    }

    function cache($sec)
    {
        if (!$this->session->_debugClientCache) {

            if ($sec > 0) {
                header('Cache-Control: must-revalidate, max-age=' . (int)$sec);
                header('Pragma: cache');
                header('Expires: ' . str_replace('+0000', 'GMT', gmdate('r', time() + $sec)));
            } else {
                header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
                header('Pragma: no-cache'); // HTTP 1.0.
                header('Expires: 0'); // Proxies.
            }
        }
    }

    function setMasterView($template)
    {
        $this->masterView = $template;
    }

    function renderData($data, $type)
    {
        $this->contentType($this->contentTypes[$type]);
        $this->write($this->serialization->serialize($type, $data));
    }
}

?>
