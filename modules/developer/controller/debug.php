<?php
namespace modules\developer\controller;

use core\Controller;

class DebugMode extends Controller{

    function home(){
        $this->response->renderView();
    }

    function toggle(){
        $this->session->_debugMode=!$this->session->_debugMode;
        return $this->response->redirectBack();
    }

    function clearCache(){
        $model=$this->model->clearCache;
        $model->clear();
        $this->response->redirect("developer/module");
    }
}

?>
