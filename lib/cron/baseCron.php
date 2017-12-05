<?php
namespace hodphp\lib\cron;
use hodphp\core\Loader;

abstract class BaseCron extends \hodphp\core\Base
{
    abstract function run();
}