<?php
namespace hodphp\lib\db;

use hodphp\core\Lib;
use hodphp\mysqli;

class Condition extends Lib
{
    var $connector = "";
    var $subConnector = "";
    var $parts = array();

    function eq($left, $right)
    {
        $this->parts[] = $this->connector . " " . $this->parse($left) . "=" . $this->parse($right);
        $this->connector = " AND ";
        return $this;
    }



    function isNull($field){
        $this->parts[] = $this->connector . " " . $this->parse($field)." IS NULL";
        $this->connector = " AND ";
        return $this;
    }
    function like($left, $right)
    {
        $this->parts[] = $this->connector . " " . $this->parse($left) . " LIKE " . $this->parse($right);
        $this->connector = " AND ";
        return $this;
    }

    function notLike($left,$right){
        $this->parts[] = $this->connector . " " . $this->parse($left) . " NOT LIKE " . $this->parse($right);
        $this->connector = " AND ";
        return $this;
    }

    //because or is reserved in php

    function parse($text)
    {
        if (substr($text, 0, 1) == "'" || substr($text, 0, 1) == '"') {
            $text = substr($text, 1);
            if (substr($text, -1) == "'" || substr($text, -1) == '"') {
                $text = substr($text, 0, -1);
            }
            $text = $this->db->escape($text);
            $text = "'" . $text . "'";
        }

        return $text;
    }

    function bOr()
    {
        $this->connector = " OR ";
        return $this;
    }

    function lteq($left, $right)
    {
        $this->parts[] = $this->connector . " " . $this->parse($left) . "<=" . $this->parse($right);
        $this->connector = " AND ";
        return $this;
    }

    function gteq($left, $right)
    {
        $this->parts[] = $this->connector . " " . $this->parse($left) . ">=" . $this->parse($right);
        $this->connector = " AND ";
        return $this;
    }

    function lt($left, $right)
    {
        $this->parts[] = $this->connector . " " . $this->parse($left) . "<" . $this->parse($right);
        $this->connector = " AND ";
        return $this;
    }

    function gt($left, $right)
    {
        $this->parts[] = $this->connector . " " . $this->parse($left) . ">" . $this->parse($right);
        $this->connector = " AND ";
        return $this;
    }

    function render()
    {
        $result = "";
        $i = 0;
        foreach ($this->parts as $part) {
            if (is_object($part)) {
                if ($i > 0) {
                    $result .= " AND ";
                }
                $result .= " (" . $part->render() . ") ";

            } else {
                $result .= $part;
            }
            $i++;
        }
        return $result;
    }

    function sub($condition)
    {
        $result = "";
        if (is_callable($condition)) {
            $subCondition = $this->db->condition();
            $condition($subCondition);
            $result = $subCondition;
        } else {
            $result = $condition;
        }
        $this->parts[] = $result;
        return $this;
    }
}

