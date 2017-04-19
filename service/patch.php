<?php
namespace hodphp\service;

use hodphp\core\Loader;
use hodphp\lib\service\BaseService;

class Patch extends BaseService
{
    var $setupDone = false;


    function setup()
    {
        if (!$this->setupDone) {
            Loader::loadClass("basePatch","lib/patch");
            $this->provider->patchlog->default->setup();
            $this->setupDone = true;
        }
    }

    function doPatchProject()
    {
        $this->doPatch("project");
    }

    function doPatch($name)
    {
        if ($name == "project") {
            $folder = "project/patch";
        } else {
            $folder = "modules/" . $name . "/patch";

            if ($this->filesystem->exists("project/modules/" . $name . "/patch")) {
                $folder = "project/modules/" . $name . "/patch";
            }
        }


        if ($this->filesystem->exists($folder)) {
            $files = $this->filesystem->getFiles($folder);
            foreach ($files as $file) {
                if (substr($file, -4)) {
                    $file = substr($file, 0, -4);
                }

                $patchName = $name . "/" . $file;
                if ($this->needPatch($patchName)) {

                    $this->goModule($name);
                    $patch = Loader::getSingleton($file, $folder);
                    $success = $patch->patch();
                    $this->goBackModule();


                    $patchModel = $this->model->patch->initialize($patchName, $success, time());
                    $this->provider->patchlog->default->save($patchModel);
                }
            }
        }
    }

    function needPatch($name)
    {
        return $this->provider->patchlog->default->needPatch($name);
    }
}

?>