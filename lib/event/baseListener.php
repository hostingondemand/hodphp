<?php
namespace framework\lib\event;
abstract class BaseListener extends \framework\core\Base
{
    abstract function handle($data);

    public function __onClassPostConstruct($data)
    {

    }

    public function __onMethodPreCall($data)
    {

    }

    public function __onMethodPostCall($data)
    {

    }

    public function __onFieldPreGet($data)
    {

    }

    public function __onFieldPostGet($data)
    {

    }

    public function __onFieldPreSet($data)
    {

    }

    public function __onFieldPostSet($data)
    {

    }
}

