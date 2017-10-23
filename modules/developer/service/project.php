<?php
namespace hodphp\modules\developer\service;

use hodphp\lib\service\BaseService;

class Project extends BaseService
{
    function create($git = null)
    {
        if (@$git) {
            $this->filesystem->mkdir("project");
            $this->git->init("project");
            $this->git->addRemote("project", "origin", $git);
            $this->git->pull("project", "master", "origin");
        } else {
            $this->filesystem->mkdir("project");
        }
    }

    function setup($project)
    {
        if (!$this->filesystem->exists("project/config")) {
            $this->filesystem->mkdir("project/config");
        }
        $this->filesystem->writeArray("project/config/server.php", array(
            "http.path" => $_SERVER["HTTP_ORIGIN"],
            "db.default.host" => $project->dbHost,
            "db.default.username" => $project->dbUser,
            "db.default.password" => $project->dbPassword,
            "db.default.db" => $project->dbDb
        ));
    }



    function updateFramework()
    {
        $this->service->module->update("framework");
    }

    function updateProject()
    {
        $this->git->pull("project", "master", "origin");
        $this->service->patch->setup();
        $this->service->patch->doPatchProject();
        $this->event->raise("projectPostUpdate", array());
    }

    function removeCache()
    {
        $this->cache->destroy();
    }

}


