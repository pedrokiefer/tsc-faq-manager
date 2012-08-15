<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/7/12
 * Time: 1:39 PM
 */
require_once("TscModel.php");

class GroupModel extends TscModel
{
    protected $_Id = NULL;
    protected $_GroupName;
    protected $_SearchBox = true;
    protected $_AskBox = true;
    protected $_Status = true;
    protected $_CreationDate;

    public static $groupTable = "CREATE TABLE %s (
        id int(11) NOT NULL AUTO_INCREMENT,
        group_name varchar(512) NOT NULL,
        search_box tinyint(1) NOT NULL DEFAULT 0,
        ask_box tinyint(1) NOT NULL DEFAULT 0,
        status tinyint(1) NOT NULL DEFAULT 1,
        creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY group_name (group_name),
        KEY status (status)
        )";

    public function validate()
    {
        $valid = true;
        if (!$this->_GroupName || empty($this->_GroupName)) {
            $this->validationMessages .= "Missing Group Name";
            $valid = false;
        }

        return $valid;
    }

    public static function load($id) {
        global $wpdb;

        $id = $wpdb->escape($id);
        $tableName = $wpdb->prefix . "tsc_faq_group";
        $query = "SELECT * FROM {$tableName} WHERE id=%d";

        $result = $wpdb->get_row($wpdb->prepare($query, $id));

        $group = new GroupModel();
        $group->Id = $result->id;
        $group->GroupName = $result->group_name;
        $group->SeachBox = $result->search_box;
        $group->AskBox = $result->ask_box;
        $group->Status = $result->status;
        $group->CreationDate = $result->creation_date;

        return $group;
    }

    public static function loadAll()
    {
        global $wpdb;
        $groups = array();

        $tableName = $wpdb->prefix . "tsc_faq_group";
        $query = "SELECT * FROM {$tableName} WHERE 1";

        $results = $wpdb->get_results($query);

        foreach ($results as $value) {
            $g = new GroupModel();
            $g->Id = $value->id;
            $g->GroupName = $value->group_name;
            $g->SeachBox = $value->search_box;
            $g->AskBox = $value->ask_box;
            $g->Status = $value->status;
            $g->CreationDate = $value->creation_date;
            $groups[] = $g;
        }

        return $groups;
    }

    public function save()
    {
        global $wpdb;

        if (!$this->validate())
            return;

        $tableName = $wpdb->prefix . "tsc_faq_group";

        $data = array(
            "group_name" => $this->_GroupName,
            "search_box" => $this->_SearchBox,
            "ask_box" => $this->_AskBox,
            "status" => $this->_Status
        );

        if ($this->_Id === NULL) {
            $wpdb->insert($tableName, $data);
            $this->_Id = $wpdb->insert_id;
        } else {
            $wpdb->update($tableName, $data, array('id' => $this->_Id));
        }

    }

    public function delete()
    {
        global $wpdb;

        if ($this->_Id === NULL)
            return;

        $tableName = $wpdb->prefix . "tsc_faq_group";

        $query = "DELETE FROM $tableName WHERE id=%d";

        $wpdb->query($wpdb->prepare($query, $this->_Id));
    }
}
