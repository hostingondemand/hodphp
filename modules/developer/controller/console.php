<?php
namespace hodphp\modules\developer\controller;

use hodphp\core\Controller;

class Console extends Controller
{
    function home(){
        $this->console->writeLine("Instructions here later");
    }

    function update($module=null){
        $this->model->console->update->process($module);
    }

    function patch(){
        $this->model->console->patch->process();
    }

    function updateDummy(){

        $this->model->console->updateDummy->process();

    }

}
?>