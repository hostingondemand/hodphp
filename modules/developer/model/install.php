<?php
namespace modules\developer\model;

use core\Controller;
use lib\model\BaseModel;

class Install extends BaseModel
{
    var $git;
    var $dbHost;
    var $dbUser;
    var $dbPassword;
    var $dbDb;

    function initialize()
    {
        $this->db_host = "localhost";
        return $this;
    }

    function install()
    {
        if ($this->git) {
            $this->shell->execute("git clone " . $this->git . " project");
        } else {
            $this->filesystem->mkdir("project");
        }
        if (!$this->filesystem->exists("project/config")) {
            $this->filesystem->mkdir("project/config");
        }
        $this->filesystem->writeArray("project/config/server.php", array(
            "http.path" => $_SERVER["HTTP_ORIGIN"],
            "db.default.host" => $this->dbHost,
            "db.default.username" => $this->dbUser,
            "db.default.password" => $this->dbPassword,
            "db.default.db" => $this->dbDb
        ));

    }



}