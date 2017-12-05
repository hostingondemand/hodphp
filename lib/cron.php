<?php
namespace hodphp\lib;
use hodphp\core\Loader;

class Cron extends \hodphp\core\Lib
{
    public function __construct()
    {
        Loader::loadClass('baseCron', 'lib\cron');
        $this->provider->cronlog->default->setup();
    }

    public function run($name)
    {
        $info = Loader::getInfo($name, 'cron');
        $annotations = $this->annotation->getAnnotationsForClass($info->type, 'interval', true);
        $canRun = true;

        if (!empty($annotations)) {
            $translated = $this->annotation->translate($annotations[0]);
            $interval = $translated->parameters[0] * 60;
            $canRun = $this->provider->cronlog->default->needCron($name, $interval);
        }

        if ($canRun) {
            $cron = Loader::createInstance($name, 'cron');
            $cron->run();
            $this->provider->cronlog->default->cronFinished($name);
        }
    }
}