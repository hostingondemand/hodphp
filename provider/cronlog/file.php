<?php
namespace hodphp\provider\cronlog;

use hodphp\lib\provider\baseprovider\BaseCronlogProvider;

class File extends BaseCronlogProvider
{
    var $dir;
    var $file;
    var $data;

    function setup()
    {
        $this->dir = "data/projectData";
        $this->file = $this->dir . "/cronlog.php";
        if (!$this->filesystem->exists($this->dir)) {
            $this->filesystem->mkDir($this->dir);
        }
        $this->data = $this->filesystem->getArray($this->file);

    }

    function cronFinished($name)
    {
        $this->data[$name] = [
            'name' => $name,
            'lastRun' => time()
        ];
    }

    function needCronInterval($name, $interval)
    {
        if (!$interval) {
            return true;
        }

        $minTime = time() - $interval;

        return (isset($this->data[$name]) && $this->data[$name]["lastRun"] < $minTime) || !isset($this->data[$name]);
    }

    function needCronSchedule($name, $schedule)
    {
        $lastRun=@$this->data[$name]["lastRun"]?:0;
        return $this->helper->schedule->needUpdate($schedule,$lastRun);
    }

    function __destruct()
    {
        $this->data = $this->filesystem->writeArray($this->file, $this->data);

    }
}

