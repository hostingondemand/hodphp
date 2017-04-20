<?php
namespace hodphp\lib\db\ensureType;
//This class just tells how an injector should look
class Date extends BaseEnsure
{
    function ensure($data, $length)
    {
        if (!$data || $data == "0000-00-00") {
            return "1970-01-01";
        }
        if (is_numeric($data)) {
            return date("Y-m-d", $data);
        }
        return $data;
    }

}

?>