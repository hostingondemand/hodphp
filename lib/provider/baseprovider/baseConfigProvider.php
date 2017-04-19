<?php
    namespace hodphp\lib\provider\baseprovider;
    abstract class BaseConfigProvider extends \hodphp\core\Lib{
        abstract function get($key,$section);
        abstract function set($key,$val,$section);
        abstract function contains($key,$section);
    }
?>