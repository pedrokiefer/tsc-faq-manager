<?php
/*
 * Plugin Name: Tecnosenior FAQ Manager
 * Plugin URI: www.tecnosenior.com
 * Description: Managing Frequently Asked Questions was Never So Easy.
 * Author: Pedro Kiefer
 * Version: 1.0
 * Author URI: http://www.pedro.kiefer.com.br
 */

/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/7/12
 * Time: 10:39 AM
 */

require_once(dirname(__FILE__) . '/TscAsyncException.php');
require_once(dirname(__FILE__) . '/controller/SettingsController.php');
require_once(dirname(__FILE__) . '/controller/GroupController.php');
require_once(dirname(__FILE__) . '/controller/QuestionController.php');

if (is_admin()) {
    require_once(dirname(__FILE__) . '/controller/GroupListTable.php');
    require_once(dirname(__FILE__) . '/controller/QuestionListTable.php');
}

class TscFaqManager
{

    static $instance;
    public $version = '1.0';
    public $groupTableName;
    public $questionTableName;
    public $settings;

    function __construct()
    {
        global $wpdb;

        self::$instance = &$this;

        $this->groupTableName = $wpdb->prefix . "tsc_faq_group";
        $this->questionTableName = $wpdb->prefix . "tsc_faq_question";
        $this->settings = &SettingsModel::load();

        add_action('admin_menu', array(&$this, 'onAdminMenu'));
        add_action('wp_head', array(&$this, 'addHeaderFiles'));
        add_shortcode('tscfaq', array(&$this, 'renderShortCode'));

        if (is_admin()) {
            wp_enqueue_script('jquery');
            wp_enqueue_script('post');
            wp_enqueue_script('editor');
            wp_enqueue_script('thickbox');
            wp_enqueue_style('thickbox');

            add_action('wp_ajax_faqAction', array(&$this, 'handleFaqActions'));
        }
    }

    /**
     * Include needed files to header
     */
    function addHeaderFiles()
    {
        $skin = $this->settings->Skin;
        require_once(__DIR__ . '/skins/' . $skin);

        if (!function_exists('tsc_skin_get_headers'))
            return;

        $headers = tsc_skin_get_headers();

        if (isset($headers['js'])) {
            foreach ($headers['js'] as $script) {
                wp_enqueue_script($script);
            }
        }

        if (isset($headers['css'])) {
            foreach ($headers['css'] as $style) {
                wp_enqueue_style($style, plugin_dir_url(__FILE__) . "/skins/" . $style);
            }
        }
    }

    /**
     * Render the short code, based on the current selected skin.
     */
    function renderShortCode($attributes)
    {

        if (!$attributes['id'])
            return false;

        $currentGroup = GroupModel::load($attributes['id']);

        if (!$currentGroup || $currentGroup->Status == 0)
            return false;

        if (isset($attributes['searchbox']))
            $currentGroup->SearchBox = $attributes['searchbox'];

        if (isset($attributes['askbox']))
            $currentGroup->SearchBox = $attributes['askbox'];

        $questions = QuestionModel::loadByGroupId($currentGroup->Id, true, true);

        $skin = $this->settings->Skin;
        require_once(__DIR__ . '/skins/' . $skin);

        return tsc_skin_render($currentGroup, $questions);
    }


    /**
     *  Create Admin Area
     */
    function onAdminMenu()
    {
        if (!is_admin())
            return;

        add_menu_page("Tecnosenior FAQs", "Tecnosenior FAQs", "administrator", "tsc_faq", array(&$this, "handle_page_faq_settings"));
        add_submenu_page('tsc_faq', 'Settings', 'Settings', 'administrator', "tsc_faq", array(&$this, "handle_page_faq_settings"));
        add_submenu_page('tsc_faq', 'Groups', 'Groups', 'administrator', 'tsc_faq_groups', array(&$this, 'handle_page_faq_groups'));
        add_submenu_page('tsc_faq', 'Questions', 'Questions', 'administrator', 'tsq_faq_questions', array(&$this, 'handle_page_faq_questions'));
    }

    public function handleFaqActions()
    {
        if (!isset($_REQUEST['req']))
            return;

        $req = explode("@", $_REQUEST['req']);
        $ctlName = $req[1] . "Controller";
        try {
            if (!class_exists($ctlName))
                throw new TscAsyncException("Invalid controller");

            $controller = new $ctlName;
            $action = $req[0];

            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                throw new TscAsyncException("Invalid request");
            }
        } catch (TscAsyncException $e) {
            header("Content-type: application/json");
            $error = array(
                "Exception" => $e->getMessage(),
                "Trace" => $e->getTrace());
            echo json_encode(array("status" => "error", "message" => $error));
        }
        exit();
    }

    function handle_page_faq_settings()
    {
        $settingsController = new SettingsController();
        $settingsController->renderPage();
    }

    function handle_page_faq_groups()
    {
        $listing = new GroupListTable($this->groupTableName, $this->questionTableName);
        $listing->prepare_items();
        include dirname(__FILE__) . '/views/faq_groups.php';
    }

    function handle_page_faq_questions()
    {
        $listing = new QuestionListTable($this->groupTableName, $this->questionTableName);
        $listing->prepare_items();
        include dirname(__FILE__) . '/views/faq_questions.php';
    }

    function install()
    {
        global $wpdb;

        $groupTable = sprintf(GroupModel::$groupTable, $this->groupTableName);
        $questionTable = sprintf(QuestionModel::$questionTable, $this->questionTableName);

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($groupTable);
        dbDelta($questionTable);

        update_option("tsc_faq_db_version", $this->version);
        SettingsModel::registerOptions();

        /* Reload settings */
        $this->settings = SettingsModel::load();
    }
}

$tscfm = new TscFaqManager();

/* Register Database */
register_activation_hook(__FILE__, array($tscfm, 'install'));
if (get_option("tsc_faq_db_version") != $tscfm->version) {
    $tscfm->install();
}