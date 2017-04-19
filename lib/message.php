<?php
namespace hodphp\lib;

use hodphp\core\Lib;

class Message extends Lib
{

    function send($message, $type = "warning")
    {
        $messages = $this->getMessages();
        if(is_object($type)){
            $type=$type->name;
        }
        if (!$messages) {
            $messages = array();
        }
        $messages[] = array("message" => $message, "type" => $type);
        $this->session->messages = $messages;
    }

    function getMessages()
    {
        return $this->session->messages;
    }

    function popMessages(){
        $messages=$this->getMessages();
        $this->session->messages=array();
        return $messages;
    }

}

?>