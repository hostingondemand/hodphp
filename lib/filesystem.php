<?php
namespace hodphp\lib;

//a simple wrapper around the filesystem to be able to use files in the right directory
use hodphp\core\Loader;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Filesystem extends \hodphp\core\Lib
{

    var $customExtensions = array("css" => "text/css", "svg" => "image/svg+xml");

    //generate a full path
    var $ignores = false;

    var $debugLevel=-1;//filter logging to avoid big overhead when logging is turned off.


    function getDebugLevel(){
        if($this->debugLevel==-1){
            $this->debugLevel= $this->debug->getLevel();
        }
        return $this->debugLevel;
    }

    function getFile($file)
    {
        if ($fullPath = $this->findRightPath($file)) {
            if ($this->getDebugLevel() <= 2) {
                $this->debug->info("read file", array("file" => $fullPath, "relativePath" => $file), "file");
            }
            return file_get_contents($fullPath);
        }
        return false;
    }

    //read a file entirely

    function findRightPath($file)
    {
        if (!(substr($file, 0, 1) == "/" || substr($file, 0, 2) == "~/")) {
            $fullPath = $this->calculatePath("project/modules/" . Loader::$module . "/" . $file);
            if (file_exists($fullPath)) {
                return $fullPath;
            }

            $fullPath = $this->calculatePath("modules/" . Loader::$module . "/" . $file);
            if (file_exists($fullPath)) {
                return $fullPath;
            }

            $fullPath = $this->calculatePath("project/" . $file);
            if (file_exists($fullPath)) {
                return $fullPath;
            }

        }

        $fullPath = $this->calculatePath($file);
        if (file_exists($fullPath)) {
            return $fullPath;
        }


        $this->debug->error("File not found:", array("file" => $file),"file");

        return false;
    }

    function calculatePath($file)
    {
        //if the string doesnt start with / or ~/ it will be considered a project file
        if (!(substr($file, 0, 1) == "/" || substr($file, 0, 2) == "~/" || substr($file, 1, 2) == ":\\")) {
            $exp = explode("/", str_replace("\\", "/", $file));
            if ($exp[0] == "project") {
                $path = DIR_PROJECT;
                unset($exp[0]);
            } elseif ($exp[0] == "modules" && @$exp[1] == "developer") {
                $path = DIR_FRAMEWORK . "/modules/";
                unset($exp[0]);
            } elseif ($exp[0] == "modules") {
                $path = DIR_MODULES;
                unset($exp[0]);
            } elseif ($exp[0] == "data") {
                $path = DIR_DATA;
                unset($exp[0]);
            } else {
                $path = DIR_FRAMEWORK;
            }

            $file = $path . implode("/", $exp);
        }

        //if it starts with ~/ it will be considered a developer file..
        if (substr($file, 0, 2) == "~/") {
            $file = str_replace("~/", $_SERVER["HOME"] . "/", $file);
        }
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $file = str_replace("/", "\\", $file);
        }
        return $file;
    }

    //check if folder or file exists

    function getContentType($file)
    {
        $exp = explode(".", $file);
        if (isset($this->customExtensions[$exp[count($exp) - 1]])) {
            return $this->customExtensions[$exp[count($exp) - 1]];
        } elseif ($fullPath = $this->findRightPath($file)) {
            return mime_content_type($fullPath);
        }
        return false;
    }

    //create a directory

    function mkDir($folder)
    {
        $path = $this->calculatePath($folder);
        if (!$this->exists($path)) {
            if ($this->getDebugLevel() <= 2) {
                $this->debug->info("Directory created", array("folder" => $path, "relativePath" => $folder), "file");
            }
            return mkdir($path, 0744, true);
        }
    }

    function exists($path)
    {
        $path = $this->calculatePath($path);
        return file_exists($path);
    }

    function getDirs($dir, $useIgnores = true)
    {
        static $dirResults;

        if (!$dirResults) {
            $dirResults = array();
        }

        $ignores = $this->getIgnores();
        $path = $this->calculatePath($dir);
        $dirs = array();
        if (!isset($dirResults[$path])) {
            if ($this->exists($path)) {
                if ($handle = opendir($path)) {
                    while (false !== ($entry = readdir($handle))) {
                        if ($entry != "." && $entry != ".." && is_dir($path . "/" . $entry)) {
                            if (!is_array($ignores) || !in_array($entry, $ignores)) {
                                $dirs[] = $entry;
                            }
                        }
                    }
                    closedir($handle);
                }
            }
            $dirResults[$path] = $dirs;
        }

        if ($this->getDebugLevel() <= 2) {
            $this->debug->info("Search for directories", array("folder" => $dir, "resultCount" => count($dirResults["path"])), "file");
        }

        return $dirResults[$path];
    }

    //create an array of all directories

    function getIgnores($useIgnores = true)
    {
        if (!$useIgnores) {
            return false;
        }
        if ($this->ignores === false) {
            $this->ignores = $this->config->get("filesystem.ignore", "server");
            if (!$this->ignores) {
                $this->ignores = array();
            }
        }
        return $this->ignores;

    }

    function getFilesRecursive($dir, $type = false)
    {
        if (!is_array($dir)) {
            $dir = array($dir);
        }
        $ignores = $this->getIgnores();
        $files = array();
        foreach ($dir as $currentDir) {
            if ($this->exists($currentDir)) {
                $path = $this->calculatePath($currentDir);
                $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS));
                foreach ($it as $path) {
                    if ((!is_array($ignores) || !in_array($path, $ignores)) && (!$type || substr($path, -strlen($type)) == $type)) {
                        $files[] = $path->getRealPath();
                    }
                }
            }
        }

        if ($this->getDebugLevel() <= 2) {
            $this->debug->info("Search for files recursively", array("folder" => $dir, "resultCount" => count($files), "filter" => $type ?: "no"), "file");
        }

        return $files;
    }

    function getFilesRecursiveWithInfo($dir, $type = false)
    {
        if (!is_array($dir)) {
            $dir = array($dir);
        }
        $ignores = $this->getIgnores();
        $files = array();
        foreach ($dir as $currentDir) {
            if ($this->exists($currentDir)) {
                $path = $this->calculatePath($currentDir);
                $realPath = $path;
                $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS));
                foreach ($it as $path) {
                    if ((!is_array($ignores) || !in_array($path, $ignores)) && (!$type || substr($path, -strlen($type)) == $type)) {
                        $addFile["absolutePath"] = $path->getRealPath();
                        $addFile["relativePath"] = str_replace($realPath, $currentDir, $addFile["absolutePath"]);
                        $addFile["path"] = str_replace($realPath, "", $path->getRealPath());
                        $files[] = $addFile;
                    }
                }
            }
        }


        if ($this->getDebugLevel() <= 2) {
            $this->debug->info("Search for files recursively", array("folder" => $dir, "resultCount" => count($files), "filter" => $type ?: "no"), "file");
        }

        return $files;
    }

    //create an array of all files
    function getFiles($dir, $type = false, $useIgnores = false)
    {
        $ignores = $this->getIgnores();
        $path = $this->calculatePath($dir);
        $files = array();
        if ($this->exists($path)) {
            if ($handle = opendir($path)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != ".." && !is_dir($dir . "/" . $entry) && (!$type || substr($entry, -strlen($type)) == $type)) {
                        if (!is_array($ignores) || !in_array($entry, $ignores)) {
                            $files[] = $entry;
                        }
                    }
                }
                sort($files, SORT_NATURAL);
                closedir($handle);
            }
        }

        if ($this->getDebugLevel() <= 2) {
            $this->debug->info("Search for files", array("folder" => $dir, "resultCount" => count($files), "filter" => $type ?: "no"), "file");
        }

        return $files;
    }

    //write to content file if file exists clear it first

    function getArray($file,$noDebug=false)
    {
        if ($path = $this->findRightPath($file)) {

            if (!$noDebug&&$this->getDebugLevel() <= 2) { //to avoid infinite loop with config.
                $this->debug->info("Read array from file", array("file" => $path, "relativePath" => $file), "file");
            }

            return include $path;
        }
        return array();

    }

    function writeArray($file, $data)
    {
        if ($this->getDebugLevel() <= 2) {
            $this->debug->info("Write array to file", array("relativePath" => $file), "file");
        }

        $serialized = "<?php return " . var_export($data, true) . ";";
        $this->clearWrite($file, $serialized);
    }

    function clearWrite($path, $content)
    {

        $fullPath = $this->calculatePath($path);
        $handle = fopen($fullPath, "w+");
        if (fwrite($handle, $content)) {
            if ($this->getDebugLevel() <= 2) {
                $this->debug->info("Write to file", array("file" => $fullPath, "relativePath" => $path), "file");
            }
        } else {
            $this->debug->error("Writing to file failed", array("file" => $fullPath, "relativePath" => $path), "file");
        }
        fclose($handle);
    }

    function append($path, $content)
    {
        $fullPath = $this->calculatePath($path);
        $handle = fopen($fullPath, "a");
        if (fwrite($handle, $content)) {
            if ($this->getDebugLevel() <= 2) {
                $this->debug->info("Append to file", array("file" => $fullPath, "relativePath" => $path), "file");
            }
        } else {
            $this->debug->error("Appending to file failed", array("file" => $fullPath, "relativePath" => $path), "file");
        }
        fclose($handle);
    }

    function getModified($file)
    {
        $path = $this->findRightPath($file);
        if ($path) {
            return filemtime($path);
        }
        return -1;
    }

    function rm($file)
    {
        $relativePath = $file;
        $file = $this->calculatePath($file);
        if ($this->exists($file)) {
            if (is_dir($file)) {
                $dir = $file;
                $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($it,
                    RecursiveIteratorIterator::CHILD_FIRST);
                foreach ($files as $currentFile) {
                    if ($currentFile->isDir()) {
                        rmdir($currentFile->getRealPath());
                    } else {
                        unlink($currentFile->getRealPath());
                    }
                }
                rmdir($dir);

                if ($this->getDebugLevel() <= 2) {
                    $this->debug->info("Removed folder", array("file" => $file, "relativePath" => $relativePath), "file");
                }
            } else {
                unlink($file);
                if ($this->getDebugLevel() <= 2) {
                    $this->debug->info("Removed file", array("file" => $file, "relativePath" => $relativePath), "file");
                }
            }

        } else {
            $this->debug->error("Failed to remove file", array("file" => $file, "relativePath" => $relativePath), "file");
        }
    }

    function md5($file)
    {
        $path = $this->calculatePath($file);
        if ($this->exists($path)) {
            return md5_file($path);
        }
        return false;
    }

    function isSame($file1, $file2)
    {
        return $this->md5($file1) == $this->md5($file2);
    }

    function codeSize($directory)
    {
        $directory = $this->calculatePath($directory);
        if ($directory && $this->exists($directory)) {
            $size = 0;
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
                $ext = $file->getExtension();
                if ($ext == "php" || $ext == "tpl") {
                    $size += $file->getSize();
                }
            }
            return $size;
        }
        return 0;
    }

    function dirSize($directory)
    {
        $directory = $this->calculatePath($directory);
        if ($directory && $this->exists($directory)) {
            $size = 0;
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
                $ext = $file->getExtension();
                $size += $file->getSize();
            }
            return $size;
        }
        return 0;
    }

    function prefixFilesWithFolder($files, $folder)
    {
        foreach ($files as $key => $file) {
            $files[$key] = $folder . $file;
        }
        return $files;
    }

    function cp($from, $to)
    {
        $from = $this->calculatePath($from);
        $to = $this->calculatePath($to);
        if ($this->exists($from)) {
            if (copy($from, $to)) {
                if ($this->getDebugLevel() <= 2) {
                    $this->debug->info("Copied file", array("from" => $from, "to" => $to), "file");
                }
            } else {
                $this->debug->error("Failed to copy file", array("from" => $from, "to" => $to), "file");
            }
        }
    }
}


