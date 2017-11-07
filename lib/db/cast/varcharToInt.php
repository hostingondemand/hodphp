<?php
namespace hodphp\lib\db\cast;
//This class just tells how an injector should look
class VarcharToInt extends BaseCaster
{
    function cast($data, $fromLength, $toLength)
    {
        if (!$data) {
            return 0;
        }
        return intval($data, 0);
    }
}

