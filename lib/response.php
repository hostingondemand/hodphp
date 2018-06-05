<?php
namespace framework\lib;
use framework\core\Loader;

class Response extends \framework\core\Lib
{

    var $partialMode = false;
    var $masterView = "main";
    var $contentTypes = [
        "json" => "application/json",
        "form" => "application/x-www-form-urlencoded",
        "xml" => "application/xml",
        "csv" => "text/csv"
    ];

    function renderAction($parameters = "")
    {

        if (func_num_args() > 1) {
            $parameters = func_get_args();
        }

        $this->debug->info("Run action", array("parameters"=>$parameters),"response");

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
            $path = \framework\core\Loader::$controller . "/" . (\framework\core\Loader::$action);
        }

        $this->debug->info("Render view", array("view"=>$path),"response");

        $content = $this->template->parseFile($path, $data);
        $this->write($this->template->parseFile($this->masterView, Array("content" => $content)));
    }

    function renderPartial($data = Array(), $path = "")
    {
        if (!is_array($data) && !is_object($data)) {
            $path = $data;
            $data = Array();
        }

        if (!$path) {
            $path = \framework\core\Loader::$controller . "/" . \framework\core\Loader::$action;
        }

        $this->debug->info("Render partial view", array("view"=>$path),"response");

        $this->write($this->template->parseFile($path, $data));
    }

    function write($string, $options = Array())
    {
        @ob_clean();
        echo $string;
    }

    function renderContent($content)
    {
        $this->debug->info("Render content", array("view"=>$content),"response");
        $this->write($this->template->parseFile($this->masterView, Array("content" => $content)));
    }

    function renderFile($data, $contentType)
    {
        $this->contentType($contentType);
        $this->write($data);
        die();
    }

    function contentType($type)
    {
        $this->header("content-type", $type);
    }

    function header($key, $value)
    {
        header($key . ": " . $value);
    }

    function renderJson($data)
    {

        $this->debug->info("Render json",array(),"response");

        $this->contentType("application/json");
        $this->write($this->serialization->serialize("json", $data));
    }

    function redirect()
    {
        $args=func_get_args();
        $route=$this->route->createRoute($args);
        $this->debug->info("Redirected",array("arguments"=>$args,"route"=>$route),"response");
        $this->header("location", $route);

        die();
    }

    function redirectBack()
    {
        $referer=$this->request->getReferer();
        $this->debug->info("Redirected back",array("url"=>$referer),"response");
        $this->header("location", $referer);
    }

    function fakeResponse($data, $function)
    {

        $this->debug->info("Started fake response",array("input",$data),"response");

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

            $this->debug->info("Changed cache time",array("seconds",$sec),"response");

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

        $this->debug->info("render file",array("type",$type),"response");

        $this->contentType($this->contentTypes[$type]);
        $this->write($this->serialization->serialize($type, $data));
    }
}


