<?php
namespace hodphp\lib\template;

use hodphp\Core\Lib;
use hodphp\Core\Loader;

class GlobalParser extends Lib
{

    function __construct()
    {
        Loader::loadClass("expressionParser", "lib\\template");
    }

    function parse($content)
    {
        $result = Array();
        while ($this->popUntilNextStatement($content, $result)) {
        }

        return $result;

    }

    //pop all text until the next statement is found
    function popUntilNextStatement(&$content, &$result)
    {
        //if there is no content left
        if (strlen($content) == 0) {
            return false;
        }

        //find the position of the next statement
        $pos = strpos($content, "{{");

        //if there is no statement
        if ($pos === false) {

            $output["type"] = "content";
            $output["content"] = $content;
            $result[] = $output;
            $content = "";

            return true;
        }

        $var = substr($content, $pos - 1, 1);
        //if the statement is escaped
        if ($pos > 0 && substr($content, $pos - 1, 1) == "\\") {
            $text = substr($content, 0, $pos - 1) . "{{";
            $starttext = substr($text, 0, 1);
            if ($starttext == "\n") {
                $text = substr($text, 1);
            }
            if (substr($text, -1) == "\n") {
                $text = substr($text, 0, -1);
            }
            $content = substr($content, $pos + 2);
            $output["type"] = "content";
            $output["content"] = $text;
            $result[] = $output;
        } else {

            $text = substr($content, 0, $pos);
            $content = substr($content, $pos + 2);

            if (substr($text, 0, 1) == "\n") {
                $text = substr($text, 1);
            }

            if (substr($text, -1) == "\n") {
                $text = substr($text, 0, -1);
            }

            if (strlen($text)) {
                $output["type"] = "content";
                $output["content"] = $text;
                $result[] = $output;
            }

            $expression = new ExpressionParser($this->popExpression($content));
            $funcContent = "";
            if ($expression->requireContent) {
                $funcContent = $this->popContent($expression->function, $content);
                if ($expression->parseContent) {
                    $funcContent = $this->parse($funcContent);
                }
            }
            $output = Array();
            $output["type"] = $expression->type;
            $output["function"] = $expression->function;
            $output["parameters"] = $expression->parameters;
            $output["content"] = $funcContent;

            $result[] = $output;
        }
        return true;
    }

    function popExpression(&$content)
    {
        $pos = $this->findExpressionEndPos($content);
        $result = $result = substr($content, 0, $pos);
        $content = substr($content, $pos + 2);
        return $result;
    }

    function findExpressionEndPos($content)
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
                if ($firstChar == "{" && $secondChar == "{") {
                    $foundOpens++;
                }
                if ($firstChar == "}" && $secondChar == "}") {
                    if ($foundOpens) {
                        $foundOpens--;
                    } else {
                        return $i;
                    }
                }
            }

        }

        return false;
    }

    function popContent($func, &$content)
    {
        $pos = $this->findClosePos($content, $func);
        $result = substr($content, 0, $pos);
        $content = substr($content, $pos + strlen($func) + 5);
        return $result;

    }

    function findClosePos($content, $func)
    {
        $strlen = strlen($content);
        $funclen = strlen($func);
        $openLength = $funclen + 3;
        $closeLength = $funclen + 5;

        $opened = 0;

        for ($i = 0; $i <= $strlen; $i++) {
            $opener = substr($content, $i, $openLength);
            $closer = substr($content, $i, $closeLength);
            if ($opener == "{{" . $func . "(") {
                $opened++;
            }

            if ($closer == "{{/" . $func . "}}") {
                if ($opened) {
                    $opened--;
                } else {
                    return $i;
                }
            }
        }
        return $i;

    }

}

?>