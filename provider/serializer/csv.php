<?php

namespace hodphp\provider\serializer;

use hodphp\lib\serialization\Serializer;

class CSV extends Serializer
{
    function serialize($data)
    {
        if (is_object($data)) {
            $data = $this->prepareObject($data);
            foreach ($data["annotated"] as $field => $value) {
                if (isset($value["_annotations"]["mainData"])) {
                    return $this->arrayToCsv($data["data"][$field], $value["_annotations"]);
                }
            }
        } elseif (is_array($data)) {
            return $this->arrayToCsv($data,[]);
        }

        return false;
    }

    function arrayToCsv($data, $annotations = [])
    {
        if($annotations["header"]){
            $header=[];
            foreach($data[0] as $key=>$val){
                $header[$key]=$key;
            }
            array_unshift($data,$header);
        }
        $delimiter=isset($annotations["delimiter"])?$annotations["delimiter"]->parameters[0]:",";
        $escape=isset($annotations["escape"])?$annotations["escape"]->parameters[0]:"\\";
        $enclosure=isset($annotations["enclosure"])?$annotations["enclosure"]->parameters[0]:"'";

        $stream=fopen("php://memory","w+");
        foreach($data as $row){
            fputcsv($stream,$row,$delimiter,$enclosure,$escape);
        }
        rewind($stream);
        while(!feof($stream)) {
            $result.=fgets($stream).PHP_EOL;
        }
        return $result;


    }

    function unserialize($data, $assoc = false, $type = null)
    {
        $this->csvToArray($data, $out);
        return $out;
    }

    function csvToArray($data = '', &$target = [], $delimiter = ',', $enclosure = '"')
    {
        $rows = [];
        $rows = str_getcsv($data, "\n");
        $headers = false;
        foreach ($rows as &$row) {
            if (!$headers) {
                $headers = str_getcsv($data, $delimiter);
            }
            $row = array_combine($headers, str_getcsv($row, $delimiter));
        }
        array_shift($rows);
        $target = $rows;
    }
}

