<?php
namespace modules\developer\controller;

use core\Controller;

class Debug extends Controller{

    function home(){
        return  $this->response->renderView();
    }

    function toggleMode(){
        $this->session->_debugMode=!$this->session->_debugMode;
        return $this->response->redirectBack();
    }
    function toggleClientCache(){
        $this->session->_debugClientCache=!$this->session->_debugClientCache;
        return $this->response->redirectBack();

    }

    function clearCache(){
        $model=$this->model->clearCache;
        $model->clear();
        return $this->response->redirectBack();
    }
}

?>
