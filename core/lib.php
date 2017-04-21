<?php
namespace hodphp\core;

class Lib extends Base
{

    var $__turboMode = true;

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

//some classes use magic get.. to avoid complicated code the core is always available through core()
//the reason lib is used and not base, is to avoid unnecessary event raising..
function core()
{
    static $base;
    if (!@$base) {
        $base = Loader::createInstance("lib", "core");
    }
    return $base;
}

//this offers an instance of the proxy of the current class
function self()
{
    return loader::getCurrentClass();
}

