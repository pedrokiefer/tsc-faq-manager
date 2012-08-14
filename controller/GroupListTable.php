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
            'singular' => 'group',
            'plural' => 'groups',
            'ajax' => true
        ));
    }

    function column_default($item, $column_name)
    {
        $actions = array(
            'edit' => sprintf("<a href=\"%s?action=faqAction&req=edit@Group&width=500&height=400&id=%s\" class=\"thickbox\">Edit</a>", admin_url('admin-ajax.php'), $item['id']),
            'delete' => sprintf("<a href=\"%s?action=faqAction&req=delete@Group&id=%s\">Delete</a>", admin_url('admin-ajax.php'), $item['id']),
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
            'group_name' => 'Name',
            'question' => 'Questions',
            'search_box' => 'Search Box',
            'ask_box' => 'Ask Box',
            'status' => 'Status',
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
            return "<span style=\"color:green\">Active</span>";
        } else {
            return "Inactive";
        }
    }

    function column_shortcode($item)
    {

    }
}
