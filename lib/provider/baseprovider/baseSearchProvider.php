<?php
namespace hodphp\lib\provider\baseprovider;
abstract class BaseSearchProvider extends \hodphp\core\Lib
{
    abstract function search($query,$keywords,$fields,$useScores);
}


