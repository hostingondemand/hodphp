<?php
namespace hodphp\helper;
use hodphp\lib\helper\BaseHelper;

class Base extends BaseHelper
{
    function calculate($input, $fromBase, $toBase, $characters = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"])
    {
        if (!$toBase) {
            $toBase = count($characters);
        }
        if (!$fromBase) {
            $fromBase = count($characters);
        }
        $map = array_flip($characters);
        $decimal = $this->decimal($input, $fromBase, $map);
        $positions = $this->findPositions($decimal, $toBase, $characters);

        return $this->draw($positions);
    }

    function findPositions($decimal, $toBase, $characters, &$result = [])
    {
        if ($decimal > 0) {
            $position = @floor(log($decimal) / log($toBase));
            $found = $this->findNumber($decimal, $position, $toBase);
            $result[$position] = $characters[$found['output']];
            $this->findPositions($found['rest'], $toBase, $characters, $result);
        }
        return $result;
    }

    function findNumber($input, $position, $toBase)
    {
        $part = pow($toBase, $position);
        $output = floor($input / $part);
        $rest = $input - ($output * $part);

        return [
            'rest' => $rest,
            'output' => $output
        ];
    }


    function draw($positions)
    {
        $positions = $this->prepare($positions);

        return implode('', $positions);
    }

    function prepare($positions)
    {
        foreach ($positions as $key => $val) {
            $number = $key;
            break;
        }
        $i = $number;
        while ($i >= 0) {
            if (empty($positions[$i])) {
                $positions[$i] = "0";
            }
            $i--;
        }

        krsort($positions);

        return $positions;
    }

    function decimal($input, $fromBase, $map)
    {
        $split = str_split($input);
        $reversed = array_reverse($split);
        $output = 0;

        foreach ($reversed as $key => $val) {
            $output += $map[$val] * pow($fromBase, $key);
        }

        return $output;
    }
}