<?php
namespace hodphp\lib\provider\baseprovider;

use hodphp\core\Lib;

abstract class BaseCronlogProvider extends Lib
{
    abstract function setup();

    abstract function cronFinished($name);

    abstract function needCronInterval($name, $interval);
    abstract function needCronSchedule($name, $schedule);
}

