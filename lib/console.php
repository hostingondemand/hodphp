<?php
namespace hodphp\lib;
use hodphp\core\Loader;

class Console extends \hodphp\core\Lib
{


    var $answered = array();

    function isConsole(){
        return php_sapi_name() == 'cli';
    }

    function options(){
        Loader::getSingleton("options","lib\console");
    }

    //some translations from human language to console commands
    private $properties = Array(
        "bold" => "\033[1m",
        "italic" => "\033[3m",
        "underline" => "\033[4m",
        "blink" => "\033[5m",
        "black" => "\033[30m",
        "red" => "\033[31m",
        "green" => "\033[32m",
        "yellow" => "\033[33m",
        "blue" => "\033[34m",
        "cyan" => "\033[36m",
        "purple" => "\033[35m",

    );

    //execute a console command
    function execute($command, $path = null)
    {
        return $this->shell->execute($command,$path);
    }


    //simply write text to the screen
    function write($string, $options = Array())
    {
        //first print the options
        $this->setProperty($options);

        //print text
        echo $string;

        //reset all options
        echo "\033[0m";
    }

    //simply write text and an enter
    function writeLine($string, $options = Array())
    {
        $this->Write($string, $options);
        echo "\n";
    }


    //handle all options given in a parameter
    function setProperty($properties)
    {
        foreach ($properties as $property) {
            if (isset($this->properties[$property])) {
                echo $this->properties[$property];
            }
        }
    }


    //ask for input
    function readLine($options = Array())
    {
        $this->setProperty($options);
        $input = readline();
        echo " \033[0m";
        return $input;
    }



    //if no option is chosen through a parameter, ask for it. give a list of choices.
    function chooseOption($question, $options, $default = false)
    {

        //first initialize all options
        $possible = array();
        foreach ($options as $option) {
            $short = $option[0];
            $long = $option[1];
            $this->options()->register($short, $long);
            $possible[$long] = true;
            if ($this->options()->$long) {
                return $long;
            }
        }

        //ask the question
        $this->console->write($question . "(", Array("bold"));
        $i = 0;

        //show all options
        foreach ($possible as $key => $val) {
            if ($i > 0) {
                $this->console->write("|", Array("bold"));
            }
            $this->console->write($key, Array("bold"));
            $i++;
        }
        $this->console->write("):", Array("bold"));

        //ask for the answer
        $answer = $this->readLine();
        if (empty($answer) && $default) {
            return $default;
        }
        if (isset($possible[$answer])) {
            return $answer;
        } else {

            //if the option is invalid
            $this->writeLine("This is not an option");
            return $this->chooseOption($question, $options);
        }
    }


    //if no option is chosen through a parameter, ask for it.
    function askOption($question, $short, $long, $default = false)
    {
        $this->options()->register($short, $long);
        if ($this->options()->$long) {
            return $this->options()->$long;
        } else {
            $answer = $this->ask($question);
            if ($answer) {
                return $answer;
            }
            return $default;
        }
    }


    //just ask a question. and eventually give the answer an id so the question wont be asked multiple times.
    function ask($question, $id = null)
    {
        if (!$id !== null) {
            if (isset($this->answered[$id])) {
                return $this->answered[$id];
            }
        }

        $this->write($question . ":", Array("bold"));
        $result = $this->readLine();
        if ($id != null) {
            $this->answered[$id] = $result;
        }
        return $result;
    }


    //get the result of an option
    function getOption($short, $long, $default = false)
    {
        $this->options()->register($short, $long, $default);
        return $this->options()->$long;
    }

    function getRoute(){
        global $argv;
        return $this->removeOptions($argv);
    }

    function removeOptions($args)
    {
        foreach ($args as $key => $val) {
            if (substr($val, 0, 1) == "-") {
                unset($args[$key]);
            }
        }
        $args = array_values($args);
        return $args;
    }

}

