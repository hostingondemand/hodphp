<?php
namespace hodphp\provider\patchlog;

use hodphp\lib\provider\baseprovider\BasePatchlogProvider;

class File extends BasePatchlogProvider
{
    var $dir;
    var $file;
    var $data;

    function setup()
    {
        $this->dir = "data/projectData";
        $this->file = $this->dir . "/patchlog.php";
        if (!$this->filesystem->exists($this->dir)) {
            $this->filesystem->mkDir($this->dir);
        }
        $this->data = $this->filesystem->getArray($this->file);

    }

    function save($patchModel)
    {
        $this->data[$patchModel->patch] = $patchModel->toArray();
    }

    function needPatch($name)
    {
        return !(isset($this->data[$name]) && $this->data[$name]["success"]);
    }

    function __destruct()
    {
        $this->data = $this->filesystem->writeArray($this->file,$this->data);

    }
}

?>