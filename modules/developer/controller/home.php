<?php
namespace modules\developer\controller;

use core\Controller;

class Home extends Controller{

    function home(){
        $this->response->renderView();
    }

}

?>