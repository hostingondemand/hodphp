<?php
namespace modules\developer\controller;
use core\Controller;
class Home extends Controller{

    function toggle(){
        $this->session->_debugMode=!$this->session->_debugMode;
    }

}
?>