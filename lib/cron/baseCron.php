<?php
namespace framework\lib\cron;
use framework\core\Loader;

abstract class BaseCron extends \framework\core\Base
{
    abstract function run();
}