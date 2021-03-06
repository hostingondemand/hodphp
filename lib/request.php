<?php

namespace framework\lib;
class Request extends \framework\core\Lib
{

    var $request;
    var $session;
    var $get;
    var $post;
    var $method;

    function __construct()
    {
        $this->request = $this->initialize($_REQUEST);
        $this->session = $this->initialize($_SESSION);
        $this->get = $this->initialize($_GET);
        $this->post = $this->initialize($_POST);
        $this->method = @$_SERVER['REQUEST_METHOD'] ?: 'GET';
        $this->files = $this->initialize($_FILES);
    }

    private function initialize(&$var)
    {
        $temp = $var;
        unset($var);
        return $temp;
    }

    public function getData($assoc = false, $type = null)
    {
        $data = $this->getRawData();
        if ($data) {
            return $this->http->parse($this->getHeaders(), $data, $assoc, $type);
        } elseif (count($this->post) > 0) {
            return array_merge($this->post, $this->files);
        }
        return $this->get;
    }

    public function getRawData()
    {
        return file_get_contents("php://input");
    }

    public function getHeaders()
    {
        if (function_exists("getallheaders")) {
            return array(array_change_key_case(getallheaders(), CASE_LOWER));
        } else {
            return array(array_change_key_case($this->getallheadersFallback(), CASE_LOWER));
        }
    }

    function getallheadersFallback()
    {
        static $headers = false;
        if (!$headers) {
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
        }
        if (isset($_SERVER["CONTENT_TYPE"])) {
            $headers["content-type"] = $_SERVER["CONTENT_TYPE"];
        }
        return $headers;
    }

    public function getHeaderByName($name)
    {
        $header = $this->getHeaders();

        if (!is_array($header)) {
            $header = $this->http->headersToArray($header);
        }

        //if the content type is set
        if (isset($header[0][$name])) {
            return $header[0][$name];
        }
        return "";
    }

    public function getClientLanguage()
    {
        return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    }

    public function getIp()
    {
        return @$_SERVER['HTTP_CLIENT_IP'] ? $_SERVER['HTTP_CLIENT_IP'] : (@$_SERVER['HTTP_X_FORWARDE‌​D_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : (@$_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : "127.0.0.1"));
    }

    public function getUrl()
    {
        return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    public function skipReferer()
    {
        $session=\framework\core\core()->session;
        $session->_fakeRefererCurrent = $this->getUrl();
        $session->_fakeRefererReferer = $this->getReferer();
    }

    public function getReferer($real = false)
    {
        $session=\framework\core\core()->session;
        if ($session->_fakeRefererReferer && !$real) {
            if ($this->getReferer(true) == $session->_fakeRefererCurrent) {
                return $session->_fakeRefererReferer;
            } else {
                return $this->getReferer(true);
            }

        } else {
            return $_SERVER["HTTP_REFERER"];
        }
    }

}

