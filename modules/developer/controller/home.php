<?php
namespace framework\modules\developer\controller;

use framework\core\Controller;

class Home extends Controller
{

    function home()
    {
        $this->response->renderView();
    }

}

