<?php
namespace lib;

//this class handles http
class Http extends \core\Lib
{
    //content type headers
    private $headersFormat;
    private $formatHeaders=array(
        "json"=>"application/json"
    );





    function __construct()
    {
        //make a flipped version of formatHeaders
        $this->headersFormat=array_flip($this->formatHeaders);
    }

    //do a post request
    function post($url, $data, $format,$auth=false){

        //initialize curl
        $ch = curl_init();

        //allow all https requests
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        //ask for headers
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //set url
        curl_setopt($ch, CURLOPT_URL,$url);

        //handle post data
        curl_setopt($ch, CURLOPT_POST, 1);
        $dataString=$this->serialization->serialize($format,$data);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$dataString);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: '.$this->formatHeaders[$format]
        ));



        //handle authentication
        if($auth){
            curl_setopt($ch,CURLOPT_USERPWD,$auth);
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

    function get($url,$auth=false){
        //initialize curl
        $ch = curl_init();

        //allow all https requests
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        //ask for headers
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //set url
        curl_setopt($ch, CURLOPT_URL,$url);



        //handle authentication
        if($auth){
            curl_setopt($ch,CURLOPT_USERPWD,$auth);
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

    public function parse($header, $body)
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
            $body= $this->serialization->unserialize($serializer,$body);
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
                if ($lKey === 0)
                    $result[$i]['http_code'] = $line;
                else
                {
                    list ($key, $value) = explode(': ', $line);
                    $result[$i][strtolower($key)] = $value;
                }
            }
        }

        return $result;
    }


}

