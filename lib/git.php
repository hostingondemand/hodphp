<?php
namespace hodphp\lib;

class Git extends \hodphp\core\Lib
{

    function addRemote($folder, $name, $url)
    {
        $result = $this->shell->execute("git remote add " . $name . " " . $url, $folder);
        return $this->result($result);
    }

    function result($result)
    {
        $resultToLower = strtolower($result);
        $error_level = $this->enum->messageType->info;
        if (strpos($resultToLower, "warning") !== false) {
            $error_level = $this->enum->messageType->warning;
        } elseif (strpos($resultToLower, "error") !== false) {
            $error_level = $this->enum->messageType->danger;
        }
        return (object)array(
            "message" => $result,
            "type" => $error_level
        );
    }

    function removeRemote($folder, $name)
    {
        $result = $this->shell->execute("git remote remove " . $name, $folder);
        return $this->result($result);
    }

    function pull($folder, $branch = "master", $remote = "origin")
    {
        $result = $this->shell->execute("git pull " . $remote . " " . $branch, $folder);
        return $this->result($result);
    }

    function init($folder)
    {
        $result = $this->shell->execute("git init", $folder);
        return $this->result($result);
    }

}

?>