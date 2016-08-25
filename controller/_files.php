<?php
    namespace controller;
    use core\Controller;

    class _files extends Controller{
        function content(){
            $file=implode("/",func_get_args());
            $content=$this->filesystem->getFile("content/".$file);
            $contentType=$this->filesystem->getContentType($file);
            $this->response->renderFile($content,$contentType);
        }
    }
?>