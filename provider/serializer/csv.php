<?php

namespace provider\serializer;
use lib\serialization\Serializer;

class CSV extends Serializer
{
    function serialize($data, $type=null){
        $this->arrayToCsv($data, $out);
        return $out;
    }

    function unserialize($data, $assoc=false, $type=null){
        $this->csvToArray($data, $out);
        return $out;
    }

    function csvToArray($file = '', &$target = [], $delimiter = ',', $enclosure = '"') {
        $rows = [];
        if (file_exists($file) && is_readable($file)) {
            $handle = fopen($file, 'r');
            $headers = fgetcsv($handle, 0, $delimiter, $enclosure);
            while (!feof($handle)) {
                $row = fgetcsv($handle, 0, $delimiter, $enclosure);
                if (is_array($row)) {
                    array_splice($row, count($headers));
                    $rows[] = array_combine($headers, $row);
                }
            }
            fclose($handle);
        } else {
            $target = [];
        }

        $target = $rows;
    }

    function arrayToCsv($data = [], &$target = '', $delimiter = ',') {
        if(is_array($data)) {
            $target = join(',', array_keys($data[0])) . "\n";

            foreach ($data as &$row) {
                foreach($row as $value) {
                    if(strpos($value, '"') !== false) {
                        $value = '"' . str_replace('"', '""', $value) . '"';
                    } elseif (strpos($value, ' ') !== false || strpos($value, "\n") !== false || strpos($value, "\r\n") !== false) {
                        $value = '"' . $value . '"';
                    }
                    $target .= $value . $delimiter;
                }
                $target = substr($target, 0, -1) . "\n";
            }
        }
    }
}

?>