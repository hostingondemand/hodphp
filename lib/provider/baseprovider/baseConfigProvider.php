<?php
namespace framework\lib\provider\baseprovider;
abstract class BaseConfigProvider extends \framework\core\Lib
{
    abstract function get($key, $section);

    abstract function set($key, $val, $section);

    abstract function contains($key, $section);
}

