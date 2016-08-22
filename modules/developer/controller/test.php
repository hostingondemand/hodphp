<?php
    namespace modules\developer\controller;

    use core\Controller;

    class Test extends Controller{

        function haha(){

            $this->response->renderContent("hoihoi");
        }

    }

?>