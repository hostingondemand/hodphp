<?php
    namespace controller;
    use core\Controller;

    class _files extends Controller{
        function content(){
            ob_clean();
            $file=implode("/",func_get_args());
            $content=$this->filesystem->getFile("content/".$file);
            $contentType=$this->filesystem->getContentType("content/".$file);
            $this->response->renderFile($content,$contentType);
        }
    }
?>