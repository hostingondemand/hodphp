<?php
namespace framework\lib;

use framework\core\Loader;

class Cron extends \framework\core\Lib
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
        $useInterval = !empty($annotations);
        if ($useInterval) {
            $translated = $this->annotation->translate($annotations[0]);
            $interval = $translated->parameters[0] * 60;
            $canRun = $this->provider->cronlog->default->needCronInterval($name, $interval);
        }

        $annotations = $this->annotation->getAnnotationsForClass($info->type, 'schedule', true);
        if (!empty($annotations) && (!$useInterval || !$canRun)) {
            $translated = $this->annotation->translate($annotations[0]);
            $schedule = $translated->parameters[0];
            $pattern = $translated->parameters[1] ?: "time";
            $canRun = $this->provider->cronlog->default->needCronSchedule($name, $schedule, $pattern);
        }


        if ($canRun) {
            $cron = Loader::createInstance($name, 'cron');
            $cron->run();
            $this->provider->cronlog->default->cronFinished($name);
        }
    }
}