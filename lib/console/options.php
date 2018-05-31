<?php
namespace framework\lib\console;

//this class is made to handle optional parameters
class Options extends \framework\core\Lib
{
    private $options;
    private $registeredOptions;

    //register an option to bind a shortcut to a full variable for example to make -l and --layout mean the same
    function register($short, $long, $default = false)
    {
        $this->registeredOptions[$long] = Array("alternative" => $short, "default" => $default);
        $this->registeredOptions[$short] = Array("alternative" => $long, "default" => $default);
    }

    //set up the options
    public function __construct()
    {
        global $argv;
        foreach ($argv as $arg) {
            if (substr($arg, 0, 1) == "-") {
                if (substr($arg, 0, 2) == "--") {
                    $option = substr($arg, 2);
                    if (strpos($option, '=') !== false) {
                        $exp = explode($option, "=");
                        $this->options[$exp[0]] = $exp[1];
                    } else {
                        $this->options[$option] = true;
                    }
                } else {

                    $option = substr($arg, 0);
                    if (strpos($option, '=') !== false) {
                        $exp = explode("=", $option);
                        $option = $exp[0];
                        $value = $exp[1];
                    } else {
                        $value = true;
                    }

                    $len = strlen($option);
                    for ($i = 0; $i < $len; $i++) {
                        if ($i == $len - 1) {
                            $this->options[$option{$i}] = $value;
                        } else {
                            $this->options[$option{$i}] = true;
                        }
                    }
                }
            }
        }
    }

    public function __get($name)
    {
        //get the value of an option
        if (isset($this->options[$name])) {
            return $this->options[$name];
        } elseif (isset($this->registeredOptions[$name]) && isset($this->options[$this->registeredOptions[$name]["alternative"]])) {
            return $this->options[$this->registeredOptions[$name]["alternative"]];
        } elseif (isset($this->registeredOptions[$name])) {
            return $this->registeredOptions[$name]["default"];
        }

        return false;

    }


}

