<?php
namespace framework\controller;
class Home extends \framework\core\controller
{
    function home($param1 = "")
    {
        if (!$this->filesystem->exists("project")) {
            $this->response->redirect("developer/install");
        }
        if ($param1) {
            $this->response->renderView(array(), "home/notFound");
        }
    }

}

