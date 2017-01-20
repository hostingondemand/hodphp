<?php
namespace lib;

//a simple wrapper around the filesystem to be able to use files in the right directory
use core\Loader;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveTreeIterator;

class Filesystem extends \core\Lib
{

    var $customExtensions = array("css" => "text/css");

    //generate a full path
    function calculatePath($file)
    {
        //if the string doesnt start with / or ~/ it will be considered a project file
        if (!(substr($file, 0, 1) == "/" || substr($file, 0, 2) == "~/" || substr($file, 1, 2) == ":\\")) {
            $file = $this->path->getApp() . "/" . $file;
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

    //read a file entirely
    function getFile($file)
    {
        if ($fullPath = $this->findRightPath($file)) {
            return file_get_contents($fullPath);
        }
        return false;
    }


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

    //check if folder or file exists
    function exists($path)
    {
        $path = $this->calculatePath($path);
        return file_exists($path);
    }


    //create a directory
    function mkDir($folder)
    {
        if (!$this->exists($this->calculatePath($folder))) {
            return mkdir($this->calculatePath($folder), 0744, true);
        }
    }

    var $ignores = false;

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


    //create an array of all directories
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
                        if ($entry != "." && $entry != ".." && is_dir($dir . "/" . $entry)) {
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

    function getFilesRecursive($dir, $type = false)
    {
        if (!is_array($dir)) {
            $dir = array($dir);
        }
        $ignores = $this->getIgnores();
        $files = array();
        foreach($dir as $currentDir) {
            if($this->exists($currentDir)) {
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
    function clearWrite($path, $content)
    {
        $path = $this->calculatePath($path);
        $handle = fopen($path, "w+");
        fwrite($handle, $content);
        fclose($handle);
    }

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
        $file = $this->calculatePath($directory);
        if ($file) {
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
