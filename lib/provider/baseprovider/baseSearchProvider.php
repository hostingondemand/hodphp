<?php
namespace framework\lib\provider\baseprovider;
abstract class BaseSearchProvider extends \framework\core\Lib
{
    abstract function search($query,$keywords,$fields,$useScores);
}


