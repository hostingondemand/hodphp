<?php
namespace framework\controller;
class FatalError extends \framework\core\controller
{
    function home()
    {
        $this->response->renderView();
    }

}

