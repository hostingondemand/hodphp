<?php
namespace lib;

//this class handles http
class Http extends \core\Lib
{
    //content type headers
    private $headersFormat;
    private $formatHeaders=array(
        "json" => "application/json",
        "form" => "application/x-www-form-urlencoded",
        "xml" => "application/xml",
        "csv" => "text/csv"
    );

    function __construct()
    {
        //make a flipped version of formatHeaders
        $this->headersFormat=array_flip($this->formatHeaders);
    }

    function requestWithInputData($type, $url, $data, $format ,$headers=array()) {
        $dataString = $this->serialization->serialize($format, $data);
        $headers = array_merge($headers, array(
            'Content-Type: ' . $this->formatHeaders[$format]
        ));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, ($type === 'put') ? CURLOPT_PUT : CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $server_output = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($server_output, 0, $header_size);
        $body = substr($server_output, $header_size);

        curl_close ($ch);
        return $this->parse($header, $body);
    }

    //do a post request
    function post($url, $data, $format ,$headers=array()){
        return $this->requestWithInputData('post', $url, $data, $format, $headers);
    }

    function put($url, $data, $format, $headers = array()){
        return $this->requestWithInputData('put', $url, $data, $format, $headers);
    }

    function get($url,$headers=array()){
        //initialize curl
        $ch = curl_init();

        //allow all https requests
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        //ask for headers
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //set url
        curl_setopt($ch, CURLOPT_URL,$url);


        if(count($headers)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }


        //execute the curl command
        $server_output = curl_exec ($ch);

        //split header and body
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($server_output, 0, $header_size);
        $body = substr($server_output, $header_size);

        //close the request
        curl_close ($ch);

        //parse the result
        return $this->parse($header,$body);
    }

    public function parse($header, $body,$assoc=false, $objectType=null)
    {
        //parse the headers to an array
        if(!is_array($header)) {
            $header = $this->headersToArray($header);
        }

        //if the content type is set
        if(isset($header[0]["content-type"])){
            //unserialize the headers
            $type=explode(";",$header[0]["content-type"])[0];
            $serializer=$this->headersFormat[$type];
            $body= $this->serialization->unserialize($serializer,$body,$assoc,$objectType);
        }
        return $body;
    }

    public function headersToArray($headerContent)
    {
        $result = array();

        //split on double enter
        $lines = explode("\r\n\r\n", $headerContent);

        for ($i = 0; $i < count($lines) -1; $i++) {
            //split on single enter
            foreach (explode("\r\n", $lines[$i]) as $lKey => $line)
            {
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
}

