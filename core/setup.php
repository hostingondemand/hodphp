<?php
namespace framework\core;
class setup extends Base
{
    function setup()
    {
        global $routerStarted;


        $maps = $this->config->get("maps.class", "components");
        if (is_array($maps)) {
            Loader::$classMaps = $maps;
        }

        $maps = $this->config->get("maps.namespace", "components");
        if (is_array($maps)) {
            Loader::$classMaps = $maps;
        }

        $debug = $this->debug;
        $event = $this->event;
        set_error_handler(array($debug, "handlePHPError"));
        register_shutdown_function(array($event, "handleShutdown"));
        if(function_exists("pcntl_signal")) {
            pcntl_signal(SIGTERM, array($event, "handleShutdown"));
        }

        ini_set('display_errors', 0);
        ini_set('xdebug.max_nesting_level', 500);
        if (!$this->session->__started) {
            $this->session->__started = true;
            $this->startSession();
        }

        $newPath = APP_MODE . "/.htaccess";
        $path = "provider/route/" . $this->provider->route->getDefaultName() . "/.htaccess";

        if (!$this->filesystem->exists($path) && $this->filesystem->exists($newPath)) {
            $this->filesystem->rm($newPath);
        } elseif (!$this->filesystem->exists($newPath) || !$this->filesystem->isSame($path, $newPath)) {
            if ($this->filesystem->exists($newPath)) {
                $this->filesystem->rm($newPath);
            }
            $this->filesystem->cp($path, $newPath);
        } else {
            if (@!$routerStarted && substr($_SERVER["SERVER_SOFTWARE"], 0, 3) == "PHP" && $this->filesystem->exists($newPath)) {
                $file = $this->filesystem->findRightPath("router.php");
                die("Php server is not configured to use router script. Please use the following file as router script: " . $file);
            }
        }
    }

    function startSession()
    {
        $this->session->_debugMode = false;
        $this->session->_debugClientCache = false;
        $this->session->_debugStacktrace = false;
        $this->session->_debugProfile = false;
        $this->event->raise("sessionStart");
    }
}
