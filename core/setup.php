<?php
namespace hodphp\core;
class setup extends Base
{
    function setup()
    {

        $maps = $this->config->get("maps.class", "components");
        if (is_array($maps)) {
            Loader::$classMaps = $maps;
        }

        $maps = $this->config->get("maps.namespace", "components");
        if (is_array($maps)) {
            Loader::$classMaps = $maps;
        }

        $debug = $this->debug;
        set_error_handler(array($debug, "handlePHPError"));
        register_shutdown_function(array($debug, "handleShutdown"));
        ini_set('display_errors', 0);
        ini_set('xdebug.max_nesting_level', 500);
        if (!$this->session->__started) {
            $this->session->__started = true;
            $this->startSession();
        }
    }

    function startSession()
    {
        $this->session->_debugMode = false;
        $this->session->_debugClientCache = false;
        $this->session->_debugStacktrace = false;
        $this->session->_debugProfile = false;
    }
}
