<?php
namespace hodphp\modules\developer\controller;

use hodphp\core\Controller;

class Home extends Controller
{

    function home()
    {
        $this->response->renderView();
    }

}

