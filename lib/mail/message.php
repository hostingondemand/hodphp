<?php
namespace framework\lib\mail;
use framework\core\Lib;

class Message extends Lib
{
    private $_to = array();
    private $_content;
    private $_subject;
    private $_headers;
    private $_files=[];

    function __construct()
    {
        $this->addHeader("Content-Type", "text/html; charset=ISO-8859-1");
    }


    function addFileContent($content,$fileName){
        $this->_files[]=(object)["content"=>$content,"fileName"=>$fileName];
        return $this;
    }

    function addFile($file){
        $content=$this->filesystem->getFile($file);
        return $this->addFileContent($content,basename($file));
    }

    function addHeader($key, $value)
    {
        $this->_headers[$key] = $value;
        return $this;
    }

    function from($email)
    {
        $this->addHeader("From", $email);
        $this->addHeader("Reply-To", $email);
        return $this;
    }

    function content($content)
    {
        $this->_content = $content;
        return $this;
    }

    function view($template, $data = array())
    {
        $this->_content = $this->template->parseFile($template, $data);
        return $this;
    }

    function subject($subject)
    {
        $this->_subject = $subject;
        return $this;
    }

    function addTo($email)
    {
        $this->_to[] = $email;
        return $this;
    }

    function to($email)
    {
        $this->_to = array($email);
        return $this;
    }

    function send()
    {
        if(!count($this->_files)) {
            mail($this->getTo(), $this->_subject, $this->_content, $this->getHeaders());
        }else{

            $bound = md5(time());
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= "Content-Type: multipart/mixed; boundary=\"" . $bound . "\"\r\n";

            $body = "This is a multi-part message in MIME format.\r\n";
            $body .= "--" . $bound . "\r\n";
            $body .= $this->getHeaders();
            $body .= "Content-Transfer-Encoding: 7bit\r\n";
            $body .= "\r\n";
            $body .= $this->_content."\r\n";
            
            foreach($this->_files as $file){
                $body .= "--" . $bound . "\r\n";
                $body .= "Content-Type: application/octet-stream; name=" . $file->fileName . "\r\n";
                $body .= "Content-Transfer-Encoding: base64\r\n";
                $body .= "Content-disposition: attachment; filename=" . $file->fileName . "\r\n";
                $body .= "\n";
                $body .= chunk_split(base64_encode($file->content)) . "\r\n";
            }

            $body .= "--" . $bound . "--";


            mail($this->getTo(), $this->_subject, $body,$headers);
        }
    }

    private function getTo()
    {
        return implode(";", $this->_to);
    }

    private function getHeaders()
    {
        $result = "";
        foreach ($this->_headers as $key => $val) {
            $result .= $key . ":" . $val . "\r\n";
        }
        return $result;
    }

}



