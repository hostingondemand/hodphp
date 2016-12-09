<?php
namespace lib\template;

use \core\Loader;
use \core\Lib;

//this is a subparser used by the globalParser
//this parser is specifically used to parse expressions
class ExpressionParser extends Lib
{
    var $parameters = Array();
    var $function = "";
    var $requireContent = false;
    var $parseContent = true;
    var $type = "value";

    function __construct($expression,$modules=array())
    {
        $this->parse($expression,$modules);
    }

    function parse($expression,$modules=array())
    {
        //first make sure the abstractionlayer for functions are loaded
        Loader::loadClass("abstractFunction", "lib\\template");

        //remove brackets from the expression
        $this->cleanupExpression($expression);


        if($this->currentLevelContains(Array("<", ">", "=", ">=", "<=", "==", "!=", "||", "&&"), $expression)) {
            $this->parameters = $this->splitComparison($expression);
            $this->type = "comparison_root";
        }else {

            //look for the first function call
            $function = $this->popFunction($expression);

            //if a functioncall is found
            if ($function) {

                //return a representation of a function call
                $this->function = $function;
                $this->parameters = $this->splitparameters($expression);
                $this->type = "function";

                //do some checks about if the content should be parsed etc.

                $funcObj = $this->getFunctionInstance($this->function, $modules = array());

                if ($funcObj) {
                    $this->requireContent = $funcObj->requireContent;
                    $this->parseContent = $funcObj->parseContent;
                }

                //if its not a function. then check if it is a comparison
            } elseif ($this->currentLevelContains(Array("["), $expression)) {
                $this->type = "array";
                $this->parameters = $this->parseArrayparameters($expression);
            } else {

                //if its not an array check if its just a value
                if (substr($expression, 0, 1) == '"' || substr($expression, 0, 1) == "'" || is_numeric($expression)) {
                    $this->type = "value";
                } else {

                    //if its none of those concider it a variable
                    $this->type = "variable";
                }

                $this->parameters[] = $expression;
            }
        }
    }


    //this function is made to handle comparisons
    function splitComparison($expression)
    {

        //first set up some variables
        $result[0][0] = Array();

        $strlen = strlen($expression);
        $foundQuotes = 0;

        $currentOr = 0;
        $currentAnd = 0;

        $startleft = 0;
        $lengthLeft = 0;
        $startright = 0;
        $currentOperator = "";


        //loop through the text letter by letter
        for ($i = 0; $i <= $strlen; $i++) {

            //set some variations on a letter
            $firstChar = substr($expression, $i, 1);
            $secondChar = substr($expression, $i + 1, 1);
            $doubleChar = substr($expression, $i, 2);

            //search for quotes to skip areas
            if ($firstChar == '"' || $firstChar == "'" || ($firstChar == "\\" && $secondChar == "'") || ($firstChar == "\\" && $secondChar == '"')) {
                $foundQuotes++;
            }

            //if we are not currently skipping ( found an even amount of quotes)
            if (!fmod($foundQuotes, 2)) {

                //if we reach the end of the string we just use the rest as the end of the comparision
                if ($strlen == $i) {
                    //decide operands
                    $result[$currentOr][$currentAnd][] = $this->buildSubComparison($expression, $startleft, $lengthLeft, $startright, $currentOperator, $i);

                    //if we find an or part just higher some values to add the comparison into the right part of the result array
                } elseif ($doubleChar == "||") {
                    //decide operands
                    $result[$currentOr][$currentAnd][] = $this->buildSubComparison($expression, $startleft, $lengthLeft, $startright, $currentOperator, $i);

                    //some variables set for next run
                    $i++;//skip next one its useless to check
                    $startleft = $i + 1;
                    $startright = $i + 1;
                    $currentAnd = 0;
                    $currentOr++;

                    //if we find an and part just higher some values to add the comparison into the right part of the result array
                } elseif ($doubleChar == "&&") {
                    //decide operands
                    $result[$currentOr][$currentAnd][] = $this->buildSubComparison($expression, $startleft, $lengthLeft, $startright, $currentOperator, $i);

                    //some variables set for next run
                    $i++;//skip next one its useless to check
                    $startleft = $i + 1;
                    $startright = $i + 1;
                    $currentAnd++;

                    //if we find any double char operators
                } elseif (in_array($doubleChar, array(">=", "<=", "==", "!="))) {
                    $currentOperator = $doubleChar;

                    $lengthLeft = $i - $startleft;
                    $i++;//skip next one its useless to check
                    $startright = $i + 1;

                    //same goes for single char operators
                } elseif (in_array($firstChar, array("<", ">", "="))) {
                    $currentOperator = $firstChar;
                    $lengthLeft = $i - $startleft;
                    $startright = $i + 1;
                }


            }
        }
        return $result;

    }


    //this function decides which operands should be used
    function buildSubComparison($expression, $startleft, $lengthLeft, $startright, $currentOperator, $i)
    {

        //if the subcomparison has only one operand
        if ($startleft == $startright) {

            //parse this operand
            $parser = new expressionParser(substr($expression, $startleft, $i - $startleft + 1));

            $result["type"] = $parser->type;
            if ($this->type == "array") {
                $result["parameters"] = $this->parseArrayparameters($parser->parameters);
            } else {
                $result["parameters"] = $parser->parameters;
            }
            if ($parser->type == "function") {
                $result["function"] = $parser->function;
                $result["content"] = "";
            }

        //if the comparison has 2 operands parse both same trick as above but for 2 sides.
        } else {

            $parser = new expressionParser(substr($expression, $startleft, $lengthLeft));
            $result["left"]["type"] = $parser->type;
            if ($this->type == "array") {
                $result["left"]["parameters"] = $this->parseArrayparameters($parser->parameters);
            } else {
                $result["left"]["parameters"] = $parser->parameters;
            }
            if ($parser->type == "function") {
                $result["left"]["function"] = $parser->function;
                $result["left"]["content"] = "";
            }

            $parser = new expressionParser(substr($expression, $startright, $i - $startright + 1));
            $result["right"]["type"] = $parser->type;
            if ($this->type == "array") {
                $result["right"]["parameters"] = $this->parseArrayparameters($parser->parameters);
            } else {
                $result["right"]["parameters"] = $parser->parameters;
            }
            if ($parser->type == "function") {
                $result["right"]["function"] = $parser->function;
                $result["right"]["content"] = "";
            }


            $result["type"] = "comparison_sub";
            $result["operator"] = $currentOperator;

        }
        return $result;

    }


    //a function to check if the string contains  this method takes an array as parameter..
    function currentLevelContains($comparers, $expression)
    {


        //some basic setup
        $result[0][0] = Array();

        $strlen = strlen($expression);
        $foundQuotes = 0;
        $openedBrackets=0;

        $currentOr = 0;
        $currentAnd = 0;


        //loop through text
        for ($i = 0; $i <= $strlen; $i++) {
            //set some variables to search for
            $firstChar = substr($expression, $i, 1);
            $secondChar = substr($expression, $i + 1, 1);
            $doubleChar = substr($expression, $i, 2);


            //decide if there should be skipped
            if ($firstChar == '"' || $firstChar == "'" || ($firstChar == "\\" && $secondChar == "'") || ($firstChar == "\\" && $secondChar == '"')) {
                $foundQuotes++;
            }

            elseif($firstChar=="("){
                $openedBrackets++;
            }elseif($firstChar==")"){
                $openedBrackets--;
            }

            //only if we are not skipping
            if (!fmod($foundQuotes, 2) && ! $openedBrackets) {
                //return true if one of the characters are found.
                foreach ($comparers as $val) {
                    if ($val == $firstChar || $val == $doubleChar) {
                        return true;
                    }
                }
            }
        }

    }


    //just remove brackets from an expression.
    function cleanupExpression(&$expression)
    {
        $expression=trim($expression," \t");
        $lastDouble = substr($expression, -2);
        $firstDouble = substr($expression, 0, 2);

        $last = substr($expression, -1);
        if ($last == ")") {
            $expression = substr($expression, 0, -1);
        }

        if ($lastDouble == "}}") {
            $expression = substr($expression, 0, -2);
        }
        if ($firstDouble == "{{") {
            $expression = substr($expression, 2);
        }
    }


    //search for a function and pop all text until the function starts
    function popFunction(&$expression)
    {
        $pos = $this->getFunctionPos($expression);
        if ($pos === false) {
            return false;
        } else {
            $result = substr($expression, 0, $pos);
            $expression = substr($expression, $pos + 1);
            return $result;
        }
    }


    //search for where the function starts
    function getFunctionPos($expression)
    {
        $strlen = strlen($expression);

        $foundQuotes = 0;

        for ($i = 0; $i <= $strlen; $i++) {
            $firstChar = substr($expression, $i, 1);
            $secondChar = substr($expression, $i + 1, 1);

            if ($firstChar == '"' || $firstChar == "'" || ($firstChar == "\\" && $secondChar == "'") || ($firstChar == "\\" && $secondChar == '"')) {
                $foundQuotes++;
            }

            if (!fmod($foundQuotes, 2)) {
                if ($firstChar == "(") {
                    return $i;
                }
            }
        }
        return false;

    }


    //split all parameters for a function and parse those.
    function splitparameters(&$expression)
    {
        $parameters = Array();
        do {
            $pos = $this->findNextSplit($expression);
            if ($pos !== false) {
                $parameterstr = substr($expression, 0, $pos);

                $expression = substr($expression, $pos + 1);
            } else {
                $parameterstr = $expression;
                $expression = "";
            }


            $parser = new ExpressionParser($parameterstr);
            $param["type"] = $parser->type;

            if ($this->type == "array") {
                $param["parameters"] = $this->parseArrayparameters($parser->parameters);
            } else {
                $param["parameters"] = $parser->parameters;
            }
            if ($parser->type == "function") {
                $param["function"] = $parser->function;
                $param["content"] = "";
            }


            $parameters[] = $param;
        } while ($pos !== false);
        return $parameters;
    }


    //array keys in arrays wont work witht his setup. just avoid this for now.
    function parseArrayparameters($param)
    {
        $param = substr($param, 0, -1);


        $exp1 = explode("[", $param);
        foreach ($exp1 as $key => $exprStr) {
            if ($key) {
                if (substr($exprStr, -1) == "]") {
                    $exprStr = substr($exprStr, 0, -1);
                }
                $parser = new ExpressionParser($exprStr);
                $param = Array();
                $param["type"] = $parser->type;
                $param["parameters"] = $parser->parameters;
                if ($parser->type == "function") {
                    $param["function"] = $parser->function;
                }
                $result[] = $param;
            } else {
                $result[] = $exprStr;
            }
        }


        return $result;

    }



    //find the next position  to split the parameters same tricks as the functions before..
    function findNextSplit($content)
    {
        $strlen = strlen($content);

        $foundQuotes = 0;
        $foundOpens = 0;

        for ($i = 0; $i <= $strlen; $i++) {
            $firstChar = substr($content, $i, 1);
            $secondChar = substr($content, $i + 1, 1);

            if ($firstChar == '"' || $firstChar == "'" || ($firstChar == "\\" && $secondChar == "'") || ($firstChar == "\\" && $secondChar == '"')) {
                $foundQuotes++;
            }

            if (!fmod($foundQuotes, 2)) {
                if ($firstChar == "(") {
                    $foundOpens++;
                }
                if ($firstChar == ")") {
                    if ($foundOpens) {
                        $foundOpens--;
                    }
                }

                if (!$foundOpens && $firstChar == ",") {
                    return $i;
                }

            }


        }

        return false;
    }

    private function getFunctionInstance($function,$modules)
    {
        $exp=explode(".",$function);
        if(count($exp)>1){
            foreach($modules as $module){
                if($module->_name==$exp[0]){
                    return $module->getFunction($exp[1]);
                }
            }
        }else{
            return Loader::getSingleton($exp[0], "provider\\templateFunction", "func");
        }

    }


}

?>