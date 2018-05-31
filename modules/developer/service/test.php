<?php
namespace framework\modules\developer\service;

use framework\core\Loader;
use framework\lib\service\BaseService;

class Test extends BaseService
{
    function cleanupTables()
    {
        $created = $this->patch->getCreated();
        foreach ($created as $name) {
            $this->db->query("drop table `" . $name . "`");
        }
    }

    function getTestInstances()
    {
        $instances = array();
        $folder = "project/test";
        $files = $this->filesystem->getFiles($folder);
        foreach ($files as $file) {
            $class = str_replace(".php", "", $file);
            $instances[] = Loader::createInstance($class, $folder);
        }
        return $instances;
    }
}

