<?php
namespace hodphp\modules\developer\controller;

use hodphp\core\Controller;

class Install extends Controller
{

    function home()
    {
        $model = $this->model->install->initialize();
        $this->response->renderView($model);
    }

    function options()
    {
        $this->response->renderView();
    }

    function install()
    {
        $model = $this->model->install->fromRequest();
        $model->install();
        $this->response->redirect("developer/module/all");
    }

    function update()
    {
        $model = $this->model->update;
        $model->update();
        $this->response->redirect("developer/module/all");
    }

}

?>
