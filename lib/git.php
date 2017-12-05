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
            $this->debug->error("Failed to run git command", array("message" => $result));
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
        // Get changed files
        $changedFiles = $this->shell->execute("git fetch; git diff origin/" . $branch . " --name-only", $folder);

        // Pull changed files
        $result = $this->shell->execute("git pull " . $remote . " " . $branch, $folder);

        // Change ownership
        if (!empty($changedFiles)) {
            foreach (explode(PHP_EOL, $changedFiles) as $file) {
                $this->filesystem->changeOwner($folder . '/' . $file);
            }
        }

        // Change ownership of .git/
        $this->filesystem->changeOwner('./git');

        return $this->result($result);
    }

    function init($folder)
    {
        $result = $this->shell->execute("git init", $folder);
        return $this->result($result);
    }

}