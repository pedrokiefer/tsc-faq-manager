<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/15/12
 * Time: 5:11 PM
 */
require_once("GenericModel.php");

class Settings extends GenericModel
{

    protected $_EnableQuestions;
    protected $_EmailNotify;
    protected $_EmailAddress;
    protected $_SaveEmailsAddresses;
    protected $_NotifyOnAnswer;
    protected $_Skin;

    public static function registerOptions()
    {
        $defaultOptions = array(
            "tsc_faq_enable_questions" => "1",
            "tsc_faq_email_notify" => "1",
            "tsc_faq_email_address" => get_option("admin_email"),
            "tsc_faq_save_emails_addresses" => "1",
            "tsc_faq_notify_on_answer" => "1",
            "tsq_faq_skin" => "default/default.php"
        );

        foreach ($defaultOptions as $key => $value) {
            if (!get_option($key)) {
                update_option($key, $value);
            }
        }
    }

    public static function load()
    {

        $settings = new Settings();

        $options = array(
            "tsc_faq_enable_questions" => "_EnableQuestions",
            "tsc_faq_email_notify" => "_EmailNotify",
            "tsc_faq_email_address" => "_EmailAddress",
            "tsc_faq_save_emails_addresses" => "_SaveEmailsAddresses",
            "tsc_faq_notify_on_answer" => "_NotifyOnAnswer",
            "tsq_faq_skin" => "_Skin"
        );

        foreach ($options as $key => $field) {
            $value = get_option($key);
            $settings->$field = $value;
        }

        return $settings;
    }

    public function save()
    {
        $options = array(
            "tsc_faq_enable_questions" => $this->_EnableQuestions,
            "tsc_faq_email_notify" => $this->_EmailNotify,
            "tsc_faq_email_address" => $this->_EmailAddress,
            "tsc_faq_save_emails_addresses" => $this->_SaveEmailsAddresses,
            "tsc_faq_notify_on_answer" => $this->_NotifyOnAnswer,
            "tsq_faq_skin" => $this->_Skin
        );

        foreach ($options as $key => $value) {
            update_option($key, $value);
        }
    }

}
