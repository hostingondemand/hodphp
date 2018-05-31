<?php namespace framework\provider\cache;

use framework\lib\provider\baseprovider\BaseCacheProvider;

class File extends BaseCacheProvider
{
    function setup()
    {
        if (!$this->filesystem->exists("data/cache")) {
            $this->filesystem->mkdir("data/cache");
        }
    }

    function getFileForName($name)
    {
        return "data/cache/" . $name . ".php";
    }

    function saveEntry($name, $data)
    {
        $filename=$this->getFileForName($name);
        $this->filesystem->writeArray($filename, $data);
    }

    function loadEntry($name)
    {
        $filename=$this->getFileForName($name);
        if($this->filesystem->exists($filename)) {
            return $this->filesystem->getArray($filename);
        }
        return false;
    }


    function clear()
    {
        $this->filesystem->rm("data/cache");
        $this->filesystem->mkdir("data/cache");
    }
}

?>