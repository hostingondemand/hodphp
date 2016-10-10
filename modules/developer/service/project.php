<?php
namespace modules\developer\service;

use core\Controller;
use lib\model\BaseModel;
use lib\service\BaseService;

class Project extends BaseService
{
    function create($git=null){
        if(@$git){
            $this->filesystem->mkdir("project");
            $this->git->init("project");
            $this->git->addRemote("project","origin", $git);
            $this->git->pull("project", "master","origin");
        }else{
            $this->filesystem->mkdir("project");
        }
    }

    function setup($project){
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


    function updateFramework(){
        $framework = $this->config->get("framework", "_repository");
        $localFramework = $this->config->get("framework.local", "repository");
        if($localFramework){
            $this->git->removeRemote(".","upstream");
            $this->git->addRemote(".","upstream", $framework);
            $this->git->removeRemote(".","origin");
            $this->git->addRemote(".","origin", $localFramework);
            $this->git->pull(".","master","upstream");
            $this->git->pull(".","master","origin");

        }else{
            $this->git->removeRemote(".","origin");
            $this->git->addRemote(".","origin", $framework);
            $this->git->pull(".","master","origin");
        }
    }

    function updateProject(){
        $this->git->pull("project","master","origin");
    }


}

?>