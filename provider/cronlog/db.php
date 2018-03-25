<?php
namespace hodphp\provider\cronlog;

use hodphp\lib\provider\baseprovider\BaseCronlogProvider;

class Db extends BaseCronlogProvider
{
    function setup()
    {
        $table = $this->patch->table("hodcron");
        if (!$table->exists()) {
            $table->addField("cron", "varchar(50)");
            $table->addField("lastRun", "int");
            $table->save();
        }
    }

    function cronFinished($name)
    {
        $prefix = $this->db->getPrefix();
        $query = $this->db->query("select id from " . $prefix . "hodcron where cron = '" . $name . "'");
        if ($query->numRows()) {
            $this->db->query("update " . $prefix . "hodcron set lastRun = " . time() . " where cron = '" . $name . "'");
        } else {
            $this->db->query("insert into " . $prefix . "hodcron set cron = '" . $name . "', lastRun = " . time());
        }
    }

    function needCronInterval($name, $interval = false)
    {
        if (!$interval) {
            return true;
        }

        $prefix = $this->db->getPrefix();
        $minTime = time() - $interval;
        $query = $this->db->query("select lastRun from " . $prefix . "hodcron where cron='" . $name . "'");

        if ($query->numRows()) {
            $row = $query->fetch();
            return $row['lastRun'] < $minTime;
        }

        return true;
    }

    function needCronSchedule($name, $schedule)
    {
        $prefix = $this->db->getPrefix();
        $query = $this->db->query("select lastRun from " . $prefix . "hodcron where cron='" . $name . "'");
        $lastRun=false;
        if ($query->numRows()) {
            $row = $query->fetch();
            $lastRun=$row['lastRun'];
        }
        return $this->helper->schedule->needUpdate($schedule,$lastRun);
    }
}