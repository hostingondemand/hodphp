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
                $this->cron->run($cron);
            }
        }
    }

    function runCached()
    {
        ob_start();

        $crons = $this->filesystem->getFilesRecursive('data/cache/', false, 'pageCache_');

        foreach ($crons as $cron) {
            $data = $this->filesystem->getArray($cron);

            if($this->cache->pageCacheNeedRefresh($data['route'], $data['ttl']) && $data['cron']) {
                Loader::loadAction($data['route']);
            }
        }

        ob_end_clean();
    }
}
