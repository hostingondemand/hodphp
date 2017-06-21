<?php
namespace hodphp\controller;
use hodphp\core\Controller;

class _files extends Controller
{
    function content()
    {
        //load files
        ob_clean();
        $file = implode("/", func_get_args());

        //set headers
        $contentType = $this->filesystem->getContentType("content/" . $file);
        $this->response->cache(900);

        if ($contentType == "text/css") {
            $content = $this->template->parse($this->filesystem->getFile("content/" . $file), []);
        }
        else {
            $content = $this->filesystem->getFile("content/" . $file);
        }

        //show content
        $this->response->renderFile($content, $contentType);
    }
}


