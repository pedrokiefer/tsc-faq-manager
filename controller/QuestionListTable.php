<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/8/12
 * Time: 3:43 PM
 */
class QuestionListTable extends WP_List_Table
{
    private $query = "SELECT q.*, g.group_name AS group_name FROM %s q
        LEFT JOIN %s g ON (q.group_id = g.id) %s";

    private $groupTable;
    private $questionTable;

    function __construct($groupTable, $questionTable)
    {
        $this->groupTable = $groupTable;
        $this->questionTable = $questionTable;

        parent::__construct(array(
            'singular' => __('question', 'tsc-faq-manager'),
            'plural' => __('questions', 'tsc-faq-manager'),
            'ajax' => true
        ));
    }

    function column_default($item, $column_name)
    {
        $actions = array(
            'edit' => sprintf("<a href=\"%s?action=faqAction&req=edit@Question&width=500&height=400&id=%s\" class=\"thickbox\" title=\"" . __("Edit Question", 'tsc-faq-manager') . "\">" . __("Edit", 'tsc-faq-manager') . "</a>",
                admin_url('admin-ajax.php'), $item['id']),
            'delete' => sprintf("<a href=\"%s?action=faqAction&req=delete@Question&id=%s\">" . __("Delete", 'tsc-faq-manager') . "</a>",
                admin_url('admin-ajax.php'), $item['id']),
        );

        switch ($column_name) {
            case 'cb':
                return sprintf('<input type="checkbox" name="cb_bulk[]" value="%s" />', $item[0]['id']);
            case 'question':
                return sprintf('%1$s %2$s', $item[$column_name], $this->row_actions($actions));
            case 'group_name':
            case 'status':
            case 'who_asked':
            case 'creation_date':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function get_columns()
    {
        $columns = array(
            'cb' => "",
            'question' => __('Question', 'tsc-faq-manager'),
            'group_name' => __('Group', 'tsc-faq-manager'),
            'status' => __('Status', 'tsc-faq-manager'),
            'who_asked' => __('Who Asked', 'tsc-faq-manager'),
            'creation_date' => __('Date', 'tsc-faq-manager')
        );

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable = array(
            'question' => array('question', true),
            'group_name' => array('group_name', false),
            'status' => array('status', false),
            'who_asked' => array('who_asked', false),
            'creation_date' => array('creation_date', false)
        );

        return $sortable;
    }

    function prepare_items()
    {
        global $wpdb;

        $this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());

        /* Paging */
        $perPage = 20;
        $totalItems = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->questionTable}"));

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

        $query = sprintf($this->query, $this->questionTable, $this->groupTable, $queryEnd);
        $data = $wpdb->get_results($query, ARRAY_A);

        $this->items = $data;
    }

    function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="cb_bulk[]" value="%s" />', $item['id']);
    }

    function column_status($item)
    {
        if ($item['status']) {
            return "<span style=\"color:green\">" . __("Active", 'tsc-faq-manager') . "</span>";
        } else {
            return __("Inactive", 'tsc-faq-manager');
        }
    }

    function column_creation_date($item)
    {
        return date(__("d/M/Y", 'tsc-faq-manager'), strtotime($item['creation_date']));
    }
}
