<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/15/12
 * Time: 2:17 PM
 */

require_once(dirname(__FILE__) . "/../models/Settings.php");

class SettingsController
{

    private $skinsDir;

    public function __construct()
    {
        $this->skinsDir = dirname(__FILE__) . '/../skins';
    }

    function save()
    {
        global $wpdb, $tscfm;

        if (!$_POST)
            return;

        $settings = &$tscfm->settings;

        $settings->EnableQuestions = isset($_POST['enable_questions']) ? true : false;
        $settings->EmailNotify = isset($_POST['email_notify']) ? true : false;
        $settings->EmailAddress = $wpdb->escape($_POST['email_address']);
        $settings->SaveEmailsAddresses = isset($_POST['save_emails_addresses']) ? true : false;
        $settings->NotifyOnAnswer = isset($_POST['notify_on_answer']) ? true : false;
        $settings->Skin = $wpdb->escape($_POST['skin']);

        $settings->save();

        header("Content-type: application/json");
        echo json_encode(array("status" => "saved", "teste"));
        exit();
    }

    public function renderPage()
    {
        global $tscfm;

        $settings = &$tscfm->settings;
        $skinsList = $this->loadSkinsData();
        include dirname(__FILE__) . '/../views/faq_settings.php';
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

    private function loadSkinsData()
    {
        $skins = array();
        $skinsDir = @ opendir($this->skinsDir);
        $skinFiles = array();

        if (!$skinsDir)
            return false;

        while (($file = readdir($skinsDir)) !== false) {
            if (substr($file, 0, 1) == '.')
                continue;
            if (!is_dir($this->skinsDir . '/' . $file)) {
                if (substr($file, -4) == ".php")
                    $skinFiles[] = $file;
            }
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

            $skins[sanitize_file_name($file)] = $skinData;
        }

        uasort($skins,
            function($a, $b)
            {
                return strnatcasecmp($a['Name'], $b['Name']);
            });

        return $skins;
    }
}
