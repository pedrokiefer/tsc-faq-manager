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
require_once(dirname(__FILE__) . '/controller/ViewController.php');
require_once(dirname(__FILE__) . '/controller/GroupController.php');
require_once(dirname(__FILE__) . '/controller/QuestionController.php');

if (is_admin()) {
    if (!class_exists('WP_List_Table')) {
        require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
    }
    require_once(dirname(__FILE__) . '/controller/GroupListTable.php');
    require_once(dirname(__FILE__) . '/controller/QuestionListTable.php');
}

class TscFaqManager
{

    static $instance;
    public $version = '1.2';
    public $groupTableName;
    public $questionTableName;
    public $settings;

    function __construct()
    {
        global $wpdb;

        self::$instance = &$this;

        $this->groupTableName = $wpdb->prefix . "tsc_faq_group";
        $this->questionTableName = $wpdb->prefix . "tsc_faq_question";
        $this->settings = &Settings::load();

        add_action('init', array(&$this, 'i18n'), 5);

        add_action('admin_menu', array(&$this, 'onAdminMenu'));
        add_action('admin_enqueue_scripts', array(&$this, 'enqueueAdminScripts'));

        add_action('wp_head', array(&$this, 'addHeaderFiles'));

        add_action('wp_ajax_faqQuery', array($this, 'handleFaqQuery'));
        add_action('wp_ajax_nopriv_faqQuery', array($this, 'handleFaqQuery'));
        add_shortcode('tscfaq', array(&$this, 'renderShortCode'));

        if (is_admin()) {
            add_action('wp_ajax_faqAction', array(&$this, 'handleFaqActions'));
        }
    }

    function i18n()
    {
        load_plugin_textdomain('tsc-faq-manager', false, plugin_basename(dirname(__FILE__)) . '/languages/');
    }

    function enqueueAdminScripts()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('post');
        wp_enqueue_script('editor');
        wp_enqueue_script('thickbox');
        wp_enqueue_style('thickbox');
        wp_enqueue_style('jquery-ui-smoothness', "https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/themes/smoothness/jquery-ui.css");
    }

    /**
     * Include needed files to header
     */
    function addHeaderFiles()
    {
        $skin = $this->settings->Skin;

        /* Basic FAQ Javascript, always include it! */
        wp_enqueue_script('tsc-faq', plugin_dir_url(__FILE__) . '/js/tsc-faq.js');
        wp_localize_script('tsc-faq', 'TscFaqAjax', array('ajaxurl' => admin_url('admin-ajax.php')));

        require_once(__DIR__ . '/skins/' . $skin);

        if (!function_exists('tsc_skin_get_headers'))
            return;

        $headers = tsc_skin_get_headers();

        if (isset($headers['js'])) {
            foreach ($headers['js'] as $script) {
                if (is_array($script)) {
                    wp_enqueue_script($script["name"], $script["file"]);
                } else {
                    wp_enqueue_script($script);
                }
            }
        }

        if (isset($headers['css'])) {
            foreach ($headers['css'] as $name => $style) {
                wp_enqueue_style($name, $style);
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

        $currentGroup = Group::load($attributes['id']);

        if (!$currentGroup || $currentGroup->Status == 0)
            return false;

        if (isset($attributes['searchbox']))
            $currentGroup->SearchBox = $attributes['searchbox'];

        if (isset($attributes['askbox']))
            $currentGroup->AskBox = $attributes['askbox'];

        /* Disable Questions if option not set */
        if ($this->settings->EnableQuestions != 1)
            $currentGroup->AskBox = 0;

        $questions = Question::loadByGroupId($currentGroup->Id, true, true);

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

        add_menu_page(__("Tecnosenior FAQs", 'tsc-faq-manager'),
            __("Tecnosenior FAQs", 'tsc-faq-manager'),
            "administrator",
            "tsc_faq",
            array(&$this, "handle_page_faq_settings"));

        add_submenu_page('tsc_faq',
            __('Settings', 'tsc-faq-manager'),
            __('Settings', 'tsc-faq-manager'),
            'administrator',
            "tsc_faq",
            array(&$this, "handle_page_faq_settings"));

        add_submenu_page('tsc_faq',
            __('Groups', 'tsc-faq-manager'),
            __('Groups', 'tsc-faq-manager'),
            'administrator',
            'tsc_faq_groups',
            array(&$this, 'handle_page_faq_groups'));

        add_submenu_page('tsc_faq',
            __('Questions', 'tsc-faq-manager'),
            __('Questions', 'tsc-faq-manager'),
            'administrator',
            'tsq_faq_questions',
            array(&$this, 'handle_page_faq_questions'));
    }

    /**
     * Handle admin ajax requests
     *
     * @throws TscAsyncException
     */
    public function handleFaqActions()
    {
        if (!isset($_REQUEST['req']))
            return;

        $req = explode("@", $_REQUEST['req']);
        $ctlName = $req[1] . "Controller";
        try {
            if (!class_exists($ctlName))
                throw new TscAsyncException(__("Invalid controller", 'tsc-faq-manager'));

            $controller = new $ctlName;
            $action = $req[0];

            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                throw new TscAsyncException(__("Invalid request", 'tsc-faq-manager'));
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

    /**
     * Handle front-end ajax request
     */
    public function handleFaqQuery()
    {
        if (!isset($_REQUEST['req']))
            die();

        if (!$_POST)
            die();

        $controller = new ViewController();
        if ($_POST['req'] == "search") {
            $controller->searchQuestion();
        } else if ($_POST['req'] == "question") {
            $controller->addNewQuestion();
        } else {
            header("Content-type: application/json");
            echo json_encode(array("status" => "error", "message" => __("Invalid request", 'tsc-faq-manager')));
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

        $groupTable = sprintf(Group::$groupTable, $this->groupTableName);
        $questionTable = sprintf(Question::$questionTable, $this->questionTableName);

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($groupTable);
        dbDelta($questionTable);

        update_option("tsc_faq_db_version", $this->version);
        Settings::registerOptions();

        /* Reload settings */
        $this->settings = Settings::load();
    }
}

$tscfm = new TscFaqManager();

/* Register Database */
register_activation_hook(__FILE__, array($tscfm, 'install'));
if (get_option("tsc_faq_db_version") != $tscfm->version) {
    $tscfm->install();
}