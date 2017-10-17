<?php
namespace hodphp\helper;
use hodphp\lib\helper\BaseHelper;

class Str extends BaseHelper
{
    function isValidEmail($email)
    {
        return !(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL));
    }

    function isGuid($id)
    {
        return preg_match("/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)\})$/i", $id) ? true : false;
    }

    function dummyText($minWords, $maxWords)
    {
        $noInterpunction = 0;

        $wordCount = rand($minWords, $maxWords);
        $result = "";
        for ($i = 0; $i < $wordCount; $i++) {
            if ($i > 0) {
                $result .= $this->chooseInterpunction();
                $result .= " ";
            }
            $result .= $this->dummyWord();
        }
        return $result;
    }

    function dummyWord()
    {
        $commonVowels = array("a", "e");
        $uncommonVowels = array("o", "u", "i");
        $veryCommon = array("d", "h", "n", "t", "r", "s");
        $common = array("w", "p", "f", "g", "j", "k", "l", "v", "b", "n", "m");
        $uncommon = array("q", "y", "x");

        $result = "";
        $length = 9 - sqrt(rand(1, 91)) + 2;
        $noVowel=0;
        for ($i = 0; $i < $length; $i++) {
            if($noVowel==1 && rand(1,2)==2 || $noVowel==2){
                if (rand(1, 4) == 1) {
                    $result .= $uncommonVowels[rand(0, count($uncommonVowels) - 1)];
                }else{
                    $result .= $commonVowels[rand(0, count($commonVowels) - 1)];
                }
                $noVowel=0;
            } else {
                if (rand(1, 80) == 80) {
                    $result .= $uncommon[rand(0, count($uncommon) - 1)];

                } elseif (rand(1, 3) == 5) {
                    $result .= $veryCommon[rand(0, count($common) - 1)];
                } else {
                    $result .= $common[rand(0, count($common) - 1)];
                }
                $noVowel++;
            }

        }
        return $result;
    }

    private function chooseInterpunction()
    {
        static $interpunction = [".", ".", ".", ".", ".", ".", ".", ".", ".", ".", ".", ".", ".", ".", ",", ".", ",", ",", ",", ",", ".", ".", ".", ".", ".", ".", ".", ".", ".", ".", ".", ".", ".", ".", ",", ".", ",", ",", ",", ",", "?", "?", "!"];
        static $count = 0;

        $iCount = count($interpunction);
        $result = "";
        $key = 8 - $count;

        if ($key < 2) {
            $key = 2;
        }

        if (rand(1, $key) == $key) {
            $result = $interpunction[rand(0, $iCount - 1)];
            $count = 0;
            return $result;
        } else {
            $count++;
        }

        return $result;
    }

    /* Remove all HTML from supplied string, replace breaks with newlines */
    function removeHTML($string, $exclude = '', $br2nl = false)
    {
        if (strpos($exclude, '<br>') === false) {
            $exclude .= '<br>';
        }

        $string = html_entity_decode($string);
        $string = strip_tags($string, $exclude);

        if ($br2nl) {
            $string = str_replace(['<br>', '<br/>', '<br />'], "\\n", $string);
        }

        return $string;
    }
}

?>
