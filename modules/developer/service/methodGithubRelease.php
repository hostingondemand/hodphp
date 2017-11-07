<?php
namespace hodphp\modules\developer\service;

use hodphp\lib\model\BaseModel;

class MethodGithubRelease extends BaseModel
{
    function install($name)
    {
        $module = $this->service->module->getModuleByName($name);
        return $this->download($module["source"],$module["folder"]);
    }

    function update($name)
    {
        $module = $this->service->module->getModuleByName($name);
        return $this->download($module["source"],$module["folder"]);
    }

    function download($source,$folder){
        $zipPath="temp/hodphp.zip";
        $tmpDirPath="temp/hodphp";
        if(!$this->filesystem->exists($folder)){
            $this->filesystem->mkdir($folder);
        }

        $url="https://api.github.com/repos/".$source."/releases/latest";
        $info=$this->http->get($url);

        $this->http->download($info->zipball_url,$zipPath);

        $this->filesystem->mkdir($tmpDirPath);
        $this->archive->extract($zipPath,$tmpDirPath);

        $dirs=$this->filesystem->getDirs($tmpDirPath);
        $this->filesystem->cp($tmpDirPath."/".$dirs[0],$folder);


        $this->filesystem->rm($zipPath);
        $this->filesystem->rm($tmpDirPath);

    }
}