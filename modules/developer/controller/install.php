<?php
namespace modules\developer\controller;

use core\Controller;

class Install extends Controller{

    function home(){
        $model=$this->model->install->initialize();
        $this->response->renderView($model);
    }

    function install(){
        $model=$this->model->install->fromRequest();
        $model->install();
        $this->response->redirect("developer/module/all");
    }

}

?>