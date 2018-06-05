<?php
namespace framework\lib;
class Language extends \framework\core\Lib
{
    var $data = array();
    var $languageInfo = array();

    function languageList()
    {
        $result = array();
        $languages = $this->config->get("language.supported", "website");
        if (!is_array($languages)) {
            $languages = array("en");
        }
        foreach ($languages as $language) {
            $result[] = $this->getInfo($language);
        }
        return $result;
    }

    function getInfo($language)
    {
        if (!isset($this->languageInfo[$language])) {
            $this->languageInfo[$language] = $this->filesystem->getArray("language/" . $language . "/_info.php");
        }
        return (object)$this->languageInfo[$language];
    }

    function setSessionLanguage($language)
    {
        $this->session->__language = $language;
    }

    function get($string, $file = "")
    {
        if ($file && !isset($data[$file])) {
            $this->load($file);
        }

        if ($file && isset($this->data[$file][$string])) {
            return $this->data[$file][$string];
        } elseif (!$file) {

            foreach ($this->data as $val) {
                if (isset($val[$string])) {
                    return $val[$string];
                }
            }
        }
        return "";
    }

    function load($file)
    {
        $lang = $this->getCurrentCode();
        if (isset($this->data[$file])) {
            $this->data[$file] = array_merge($this->data[$file], $this->filesystem->getArray("language/" . $lang . "/" . $file . ".php"));
        } else {
            $this->data[$file] = $this->filesystem->getArray("language/" . $lang . "/" . $file . ".php");
        }

        //english as fallback language.
        if ($lang != "en") {
            $this->data[$file] = array_merge($this->filesystem->getArray("language/en/" . $file . ".php"), $this->data[$file]);
        }

    }

    function getCurrentCode()
    {
        if ($this->session->__language) {
            $lang = $this->session->__language;
        } else {
            $lang = $this->config->get("language", "website");
        }

        //if language settings are missing
        if (!$lang) {
            $lang = "en";
        }
        return $lang;
    }

}

