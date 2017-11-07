<?php
namespace hodphp\lib;

use hodphp\core\Lib;
use hodphp\core\Loader;

//this is a simple service to write some config to the harddrive
class Config extends Lib
{

    var $data;
    var $invalididated;
    var $configProvider;

    function get($key, $section = "global", $default = "")
    {
        $this->setConfigProvider();
        if ($this->configProvider && $this->configProvider->contains($key, $section)) {
            return $this->configProvider->get($key, $section);
        }

        if (!isset($this->data[$section])) {
            if($this->filesystem) { //this is needed to avoid problems with early loading
                $this->data[$section] = $this->filesystem->getArray("config/" . $section . ".php",true);
            }
        }

        if (isset($this->data[$section][$key])) {
            return $this->data[$section][$key];
        }

        return $default;
    }

    //set a variable

    function setConfigProvider()
    {
        static $configProviderLoaded = false;

        if (!$configProviderLoaded && Loader::$classMaps) {
            $configProviderLoaded = true;
            $provider = $this->get("provider.config", "components");
            $this->configProvider = $this->provider->config->$provider;
        }
    }

    function set($key, $val, $section = "global")
    {
        $this->setConfigProvider();

        if ($this->configProvider) {
            $this->debug->info("Changed config item", array("files"=>"All"),"config");
            $this->configProvider->set($key, $section, $val);
        } else {
            $this->data[$section][$key] = $val;
            $this->invalidated = true;
        }
    }

}