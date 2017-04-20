<?php
namespace hodphp\lib;

//a simple wrapper around the filesystem to be able to use files in the right directory
use hodphp\core\Loader;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Filesystem extends \hodphp\core\Lib
{

    var $customExtensions = array("css" => "text/css");

    //generate a full path
    var $ignores = false;

    function getFile($file)
    {
        if ($fullPath = $this->findRightPath($file)) {
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
        if (!$this->exists($this->calculatePath($folder))) {
            return mkdir($this->calculatePath($folder), 0744, true);
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

        return $files;
    }

    //write to content file if file exists clear it first

    function getArray($file)
    {

        if ($path = $this->findRightPath($file)) {
            return include $path;
        }
        return array();

    }

    function writeArray($file, $data)
    {
        $serialized = "<?php return " . var_export($data, true) . ";";
        $this->clearWrite($file, $serialized);
    }

    function clearWrite($path, $content)
    {
        $path = $this->calculatePath($path);
        $handle = fopen($path, "w+");
        fwrite($handle, $content);
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
            } else {
                unlink($file);
            }
        }
    }

    function dirSize($directory)
    {
        $directory = $this->calculatePath($directory);
        if ($directory && $this->exists($directory)) {
            $size = 0;
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
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
}

?>
