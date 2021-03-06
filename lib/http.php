<?php
namespace framework\lib;

//this class handles http
class Http extends \framework\core\Lib
{
    //content type headers
    private $headersFormat;
    /**
     * @var callable
     */
    private $_proxy = false;
    private $formatHeaders = array(
        "json" => "application/json",
        "form" => "application/x-www-form-urlencoded",
        "xml" => "application/xml",
        "csv" => "text/csv"
    );

    function __construct()
    {
        //make a flipped version of formatHeaders
        $this->headersFormat = array_flip($this->formatHeaders);
        $this->headersFormat['text/xml'] = $this->headersFormat['application/xml'];
    }

    function proxyResponse($body,$headers=[]){
        if(is_array($headers)){
            $newHeaders="200\r\n";
            foreach($headers as $key=>$val){
                $newHeaders.=$key.":".$val."\r\n";
            }
            $headers=$newHeaders."\r\n\r\n";
        }
        return (object)["body"=>$body,"headers"=>$headers];
    }

    function proxy($callable)
    {
        $this->_proxy = $callable;
    }

    function post($url, $data, $format, $headers = array(), $raw = false)
    {
        return $this->requestWithInputData('post', $url, $data, $format, $headers, $raw);
    }

    //do a post request
    function requestWithInputData($method, $url, $data, $format, $headers = array(), $raw = false, $ignoreProxy = false)
    {


        $dataString = $this->serialization->serialize($format, $data);

        if (!in_array('Content-Type: ' . $this->formatHeaders[$format], $headers) && !empty($format)) {
            $headers = array_merge($headers, array(
                'Content-Type: ' . $this->formatHeaders[$format]
            ));
        }

        if ($this->_proxy && !$ignoreProxy) {
            $proxyMethod=$this->_proxy;
            $result = ($proxyMethod)(strtoupper($method), $url, $data, $headers);
            $header = $result->headers;
            $body = $result->body;
        } else {


            $ch = curl_init();

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            if ($method == 'put') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            } else {
                curl_setopt($ch, CURLOPT_POST, 1);
            }
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $server_output = curl_exec($ch);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($server_output, 0, $header_size);
            $body = substr($server_output, $header_size);

            $this->debug->info("http." . $method, [
                'url' => $url,
                'requestHeaders' => $headers,
                'requestBody' => $dataString,
                'responseHeaders' => $header,
                'responseBody' => $body
            ], "http");

            curl_close($ch);
        }


        if ($raw) {
            return (object)array("body" => $body, "header" => $header);
        }
        return $this->parse($header, $body);
    }

    public function parse($header, $body, $assoc = false, $objectType = null)
    {
        //parse the headers to an array
        if (!is_array($header)) {
            $header = $this->headersToArray($header);
        }

        //if the content type is set
        if (isset($header[0]["content-type"])) {
            //unserialize the headers
            $type = explode(";", $header[0]["content-type"])[0];
            $serializer = $this->headersFormat[$type];
            $body = $this->serialization->unserialize($serializer, $body, $assoc, $objectType);
        }
        return $body;
    }

    public function headersToArray($headerContent)
    {
        $result = array();

        //split on double enter
        $lines = explode("\r\n\r\n", $headerContent);

        for ($i = 0; $i < count($lines) - 1; $i++) {
            //split on single enter
            foreach (explode("\r\n", $lines[$i]) as $lKey => $line) {
                if ($lKey === 0) {
                    $result[$i]['http_code'] = $line;
                } else {
                    list ($key, $value) = explode(': ', $line);
                    $result[$i][strtolower($key)] = $value;
                }
            }
        }

        return $result;
    }

    function put($url, $data, $format, $headers = array(), $raw = false)
    {
        return $this->requestWithInputData('put', $url, $data, $format, $headers, $raw);
    }

    function download($from, $to)
    {
        $data = $this->get($from, [], [], true);
        $this->filesystem->clearWrite($to, $data->body);
    }

    function get($url, $headers = array(), $data = null, $raw = false, $ignoreProxy = false)
    {

        if ($this->_proxy && !$ignoreProxy) {
            $proxyMethod=$this->_proxy;
            $result = ($proxyMethod)("GET", $url, $data, $headers);
            $header = $result->headers;
            $body = $result->body;
        } else {

            if (!empty($data)) {
                $suffix = '?';
                foreach ($data as $k => $v) {
                    $suffix .= (($suffix !== '?') ? '&' : '') . urlencode($k) . '=' . urlencode($v);
                }
                $url .= $suffix;
            }

            $ch = curl_init();

            //allow all https requests
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            //ask for headers
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1800);
            curl_setopt($ch, CURLOPT_USERAGENT, "	Mozilla/5.0");
            //set url
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            if (count($headers)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }

            //execute the curl command
            $server_output = curl_exec($ch);

            //split header and body
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($server_output, 0, $header_size);
            $body = substr($server_output, $header_size);

            $this->debug->info("http.get", [
                'url' => $url,
                'requestHeaders' => $headers,
                'responseHeaders' => $header,
                'responseBody' => $body
            ], "http");

            //close the request
            curl_close($ch);
        }
        if ($raw) {
            return (object)array("body" => $body, "header" => $header);
        }
        //parse the result
        return $this->parse($header, $body);
    }

    function delete($url, $headers)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($server_output, 0, $header_size);
        $body = substr($server_output, $header_size);

        curl_close($ch);

        return $this->parse($header, $body);
    }
}
