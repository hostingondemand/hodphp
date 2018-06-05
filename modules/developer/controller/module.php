<?php
namespace framework\modules\developer\controller;

use framework\core\Controller;

class Module extends Controller
{

    function home()
    {
        $model = $this->model->module->moduleList->initialize();

        $this->response->renderView($model);
    }

    function install($name)
    {
        $this->model->module->install->process($name);
        $this->response->redirect("", "", "home");
    }

    function update($name)
    {
        $this->model->module->update->process($name);
        $this->response->redirect("", "", "home");
    }

    function all()
    {
        $this->model->module->all->process();
        $this->response->redirect("", "", "home");
    }

}

