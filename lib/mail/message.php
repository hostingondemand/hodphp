<?php
namespace framework\lib\mail;
use framework\core\Lib;

class Message extends Lib
{
    private $_to = array();
    private $_content;
    private $_subject;
    private $_headers;

    function __construct()
    {
        $this->addHeader("MIME-Version", "1.0");
        $this->addHeader("Content-Type", "text/html; charset=ISO-8859-1");
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
        mail($this->getTo(), $this->_subject, $this->_content, $this->getHeaders());
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



