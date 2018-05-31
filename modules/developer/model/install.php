<?php
namespace framework\modules\developer\model;

use framework\lib\model\BaseModel;

class Install extends BaseModel
{
    var $git;
    var $dbHost;
    var $dbUser;
    var $dbPassword;
    var $dbDb;

    function initialize()
    {
        $this->dbHost = "localhost";
        return $this;
    }

    function install()
    {
        $this->service->project->create(@$this->git);
        $this->service->project->setup($this);
        $this->service->project->updateFramework();
        $this->service->project->removeCache();
    }

}
