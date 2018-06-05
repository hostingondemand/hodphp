<?php
namespace framework\modules\developer\controller;

use framework\core\Controller;

class Debug extends Controller
{

    function home()
    {
        $model = $this->model->debug->home->initialize();
        return $this->response->renderView($model);
    }

    function toggleMode()
    {
        $mode = $this->session->_debugMode;
        if ($mode) {
            $this->session->_debugMode = false;
            $this->session->_debugClientCache = false;
            $this->session->_debugStacktrace = false;
            $this->session->_debugProfile = false;
        } else {
            $this->session->_debugMode = true;
        }

        return $this->response->redirectBack();
    }

    function toggleClientCache()
    {
        $this->session->_debugClientCache = !$this->session->_debugClientCache;
        return $this->response->redirectBack();

    }

    function toggleStackTracing()
    {
        $this->session->_debugStacktrace = !$this->session->_debugStacktrace;
        return $this->response->redirectBack();
    }

    function toggleProfiling()
    {
        $this->session->_debugProfile = !$this->session->_debugProfile;
        return $this->response->redirectBack();
    }

    function clearCache()
    {
        $model = $this->model->clearCache;
        $model->clear();
        return $this->response->redirectBack();
    }
}


