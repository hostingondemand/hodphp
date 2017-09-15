<?php
namespace hodphp\lib;
class Request extends \hodphp\core\Lib
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
        $this->method = $_SERVER['REQUEST_METHOD'];
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
        if($data) {
            return $this->http->parse($this->getHeaders(), $data, $assoc, $type);
        }elseif(count($_POST)>0){
            return $_POST;
        }
        return $_GET;
    }

    public function getRawData()
    {
        return file_get_contents("php://input");
    }

    public function getHeaders()
    {
        if(function_exists("getallheaders")) {
            return array(array_change_key_case(getallheaders(), CASE_LOWER));
        }else{
            return array(array_change_key_case( $this->getallheadersFallback(), CASE_LOWER));
        }
    }

    function getallheadersFallback(){
        static $headers = false;
        if(!$headers) {
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
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

    public function getIp()
    {
        return @$_SERVER['HTTP_CLIENT_IP'] ? $_SERVER['HTTP_CLIENT_IP'] : (@$_SERVER['HTTP_X_FORWARDE‌​D_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : (@$_SERVER['REMOTE_ADDR']?$_SERVER['REMOTE_ADDR']:"127.0.0.1"));
    }

    public function getReferer()
    {
        return $_SERVER["HTTP_REFERER"];
    }

}

