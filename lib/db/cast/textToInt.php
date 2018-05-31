<?php
namespace framework\lib\db\cast;
//This class just tells how an injector should look
class TextToInt extends BaseCaster
{
    function cast($data, $fromLength, $toLength)
    {
        if (!$data) {
            return 0;
        }
        return intval($data, 0);
    }
}

