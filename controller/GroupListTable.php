<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/7/12
 * Time: 2:40 PM
 */
class GroupListTable extends WP_List_Table
{

    private $query = "SELECT g.*, COUNT(q.id) AS question from %s g
        LEFT JOIN %s q ON (g.id = q.group_id) %s GROUP BY g.id %s";

    private $groupTable;
    private $questionTable;

    function __construct($groupTable, $questionTable)
    {
        $this->groupTable = $groupTable;
        $this->questionTable = $questionTable;

        parent::__construct(array(
            'singular' => __('group', 'tsc-faq-manager'),
            'plural' => __('groups', 'tsc-faq-manager'),
            'ajax' => true
        ));
    }

    function column_default($item, $column_name)
    {
        $actions = array(
            'edit' => sprintf("<a href=\"%s?action=faqAction&req=edit@Group&width=500&height=450&id=%s\" class=\"thickbox\" title=\"" . __("Edit Group", 'tsc-faq-manager') . "\">" . __("Edit", 'tsc-faq-manager') . "</a>",
                admin_url('admin-ajax.php'), $item['id']),
            'editOrder' => sprintf("<a href=\"%s?action=faqAction&req=editOrder@Group&width=500&height=500&id=%s\" class=\"thickbox\" title=\"" . __("Order Questions", 'tsc-faq-manager') . "\">" . __("Order Questions", 'tsc-faq-manager') . "</a>",
                admin_url('admin-ajax.php'), $item['id']),
            'delete' => sprintf("<a href=\"%s?action=faqAction&req=delete@Group&id=%s\">" . __("Delete", 'tsc-faq-manager') . "</a>",
                admin_url('admin-ajax.php'), $item['id']),
        );

        switch ($column_name) {
            case 'group_name':
                return sprintf('%1$s %2$s', $item[$column_name], $this->row_actions($actions));
            case 'question':
            case 'search_box':
            case 'ask_box':
            case 'status':
                return $item[$column_name];
            case 'shortcode':
                return sprintf("[tscfaq id=%d]", $item['ID']);
            default:
                return print_r($item, true);
        }
    }

    function get_columns()
    {
        $columns = array(
            'group_name' => __('Name', 'tsc-faq-manager'),
            'question' => __('Questions', 'tsc-faq-manager'),
            'search_box' => __('Search Box', 'tsc-faq-manager'),
            'ask_box' => __('Ask Box', 'tsc-faq-manager'),
            'status' => __('Status', 'tsc-faq-manager'),
            'shortcode' => 'Shortcode'
        );

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable = array(
            'group_name' => array('group_name', true),
            'question' => array('question', false),
            'search_box' => array('search_box', false),
            'ask_box' => array('ask_box', false),
            'status' => array('status', false)
        );
        return $sortable;
    }

    function prepare_items()
    {
        global $wpdb;

        $this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());

        /* Paging */
        $perPage = 20;
        $totalItems = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->groupTable}"));

        $paged = $this->get_pagenum();

        $totalPages = ceil($totalItems / $perPage);

        $this->set_pagination_args(array(
            "total_items" => $totalItems,
            "total_pages" => $totalPages,
            "per_page" => $perPage
        ));

        /* Sort */
        $orderBy = !empty($_GET["orderby"]) ? $wpdb->escape($_GET["orderby"]) : 'ASC';
        $order = !empty($_GET["order"]) ? $wpdb->escape($_GET["order"]) : '';

        /* Query */
        $queryEnd = "";
        if (!empty($orderBy) && !empty($order))
            $queryEnd .= " ORDER BY " . $orderBy . ' ' . $order;

        if (!empty($paged) && !empty($perPage)) {
            $offset = ($paged - 1) * $perPage;
            $queryEnd .= " LIMIT " . (int)$offset . ',' . (int)$perPage;
        }

        $query = sprintf($this->query, $this->groupTable, $this->questionTable, ' ', $queryEnd);
        $data = $wpdb->get_results($query, ARRAY_A);

        $this->items = $data;
    }

    function column_status($item)
    {
        if ($item['status']) {
            return "<span style=\"color:green\">" . __("Active", 'tsc-faq-manager') . "</span>";
        } else {
            return __("Inactive", 'tsc-faq-manager');
        }
    }

    function column_shortcode($item)
    {

    }
}
