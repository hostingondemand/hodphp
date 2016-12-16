<?php

namespace lib\serialization;
//a representation of a serializer
abstract class Serializer extends \core\Lib
{
    abstract function serialize($data, $type = null,$original=null);
    abstract function unserialize($data, $assoc = false, $type = null);
}

?>