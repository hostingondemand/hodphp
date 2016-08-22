<?php

namespace lib\Serialization;
//a representation of a serializer
abstract class Serializer extends \core\Lib
{
    abstract function serialize($data);
    abstract function unserialize($data,$assoc=false);

}

?>