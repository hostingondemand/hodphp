<?php
    namespace lib\event;
    abstract class BaseListener extends \core\Base{
        abstract function handle($data);
    }
?>