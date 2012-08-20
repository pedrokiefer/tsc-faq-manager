<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/20/12
 * Time: 10:25 AM
 */
class Skin
{
    private $skinsDir;

    function __construct()
    {
        $this->skinsDir = dirname(__FILE__) . '/../skins';
    }

    private function parseSkinData($file)
    {
        $defaultHeader = array(
            "Name" => "Skin Name",
            "Description" => "Description",
            "Version" => "Version",
            "Author" => "Author",
            "AuthorURI" => "Author URI"
        );

        $skinData = get_file_data($file, $defaultHeader);

        return $skinData;
    }

    public function loadSkinsData()
    {
        $skins = array();
        $skinsDir = @ opendir($this->skinsDir);
        $skinFiles = array();

        if (!$skinsDir)
            return false;

        while (($file = readdir($skinsDir)) !== false) {
            if (substr($file, 0, 1) == '.')
                continue;
            if (!is_dir($this->skinsDir . '/' . $file))
                continue;

            $pluginSubdir = @ opendir($this->skinsDir . "/" . $file);
            if (!$pluginSubdir)
                continue;
            while (($subfile = readdir($pluginSubdir)) !== false) {
                if (substr($subfile, 0, 1) == '.')
                    continue;
                if (substr($subfile, -4) == ".php")
                    $skinFiles[] = "$file/$subfile";
            }
            closedir($pluginSubdir);
        }
        closedir($skinsDir);

        if (empty($skinFiles))
            return false;

        foreach ($skinFiles as $file) {
            if (!is_readable($this->skinsDir . '/' . $file))
                continue;

            $skinData = $this->parseSkinData($this->skinsDir . '/' . $file);

            if (empty($skinData))
                continue;

            $skins[$file] = $skinData;
        }

        uasort($skins,
            function($a, $b)
            {
                return strnatcasecmp($a['Name'], $b['Name']);
            });

        return $skins;
    }
}
