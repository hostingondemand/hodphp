<?php
namespace hodphp\controller;
use hodphp\core\Controller;
use hodphp\core\Loader;

class _cron extends Controller
{
    function home($name = false)
    {
        if (!$name || $name == 'cache') {
            $this->runCached();
        }

        if ($name != 'cache') {
            $this->runFiles($name);
        }
    }

    function runFiles($name)
    {
        if ($name) {
            $this->cron->run($name);
        } else {
            $crons = $this->filesystem->getProjectFiles('cron/', false, false, false);

            foreach ($crons as $cron) {
                $cron = str_replace('.php', '', $cron);
                try {
                    $this->cron->run($cron);
                }catch(\Exception $ex){}
            }
        }
    }

    function runCached()
    {
        ob_start();

        $crons = $this->filesystem->getFilesRecursive('data/cache/', false, 'pageCache_');

        foreach ($crons as $cron) {
            $data = $this->filesystem->getArray($cron);

            if($this->cache->pageCacheNeedRefresh($data['route'], $data['ttl'],$data['user']) && $data['cron']) {
                $this->auth->setup($data["user"]);
                try {
                    Loader::loadAction($data['route']);
                }catch(\Exception $ex){}
                $this->auth->setup();
            }
        }

        ob_end_clean();
    }
}