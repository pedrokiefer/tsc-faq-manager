<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/15/12
 * Time: 2:17 PM
 */

require_once(dirname(__FILE__) . "/../models/Settings.php");
require_once(dirname(__FILE__) . "/../models/Skin.php");

class SettingsController
{

    private $skins;

    public function __construct()
    {
        $this->skins = new Skin();
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
        $skinsList = $this->skins->loadSkinsData();
        include dirname(__FILE__) . '/../views/faq_settings.php';
    }

}
