<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/7/12
 * Time: 1:40 PM
 */
require_once("GenericModel.php");

class Question extends GenericModel
{

    protected $_Id = NULL;
    protected $_GroupId;
    protected $_QuestionOrder = 0;
    protected $_Question;
    protected $_WhoAsked;
    protected $_Answer;
    protected $_Status;
    protected $_Type;
    protected $_CreationDate;

    public static $questionTable = "CREATE TABLE %s (
        id int(11) NOT NULL AUTO_INCREMENT,
        group_id int(11) NOT NULL DEFAULT 0,
        question_order int(11) NOT NULL DEFAULT 0,
        question varchar(1024) NOT NULL,
        who_asked varchar(512) NOT NULL,
        answer text NOT NULL,
        status tinyint(1) NOT NULL DEFAULT 1,
        question_type tinyint(1) NOT NULL DEFAULT 0,
        creation_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY group_id (group_id),
        KEY question_order (question_order),
        KEY status (status),
        FULLTEXT question_index (question, answer)
        ) ENGINE=MyISAM";

    public function validate()
    {
        $valid = true;

        if (!$this->_Question || empty($this->_Question)) {
            $this->validationMessages .= __("Missing Question", 'tsc-faq-manager') . "<br/>";
            $valid = false;
        }

        if (!$this->_GroupId || empty($this->_GroupId)) {
            $this->validationMessages .= __("Missing Group", 'tsc-faq-manager') . "<br/>";
            $valid = false;
        }

        return $valid;
    }

    public static function load($id)
    {
        global $wpdb;

        $id = $wpdb->escape($id);
        $tableName = $wpdb->prefix . "tsc_faq_question";
        $query = "SELECT * FROM {$tableName} WHERE id=%d";

        $result = $wpdb->get_row($wpdb->prepare($query, $id));

        $question = new Question();
        $question->_Id = $result->id;
        $question->_GroupId = $result->group_id;
        $question->_QuestionOrder = $result->question_order;
        $question->_Question = $result->question;
        $question->_WhoAsked = $result->who_asked;
        $question->_Answer = $result->answer;
        $question->_Status = $result->status;
        $question->_Type = $result->question_type;
        $question->_CreationDate = $result->creation_date;

        return $question;
    }

    public static function updateOrderForId($id, $order)
    {
        global $wpdb;

        $tableName = $wpdb->prefix . "tsc_faq_question";

        $wpdb->update($tableName, array("question_order" => $order), array("id" => $id), array("%d"), array("%d"));
    }

    public static function loadByGroupId($groupId, $answered = false, $published = false)
    {
        global $wpdb;
        $questions = array();

        $where = false;
        $whereArr = array();

        if ($answered)
            $whereArr[] = "answer != ''";

        if ($published)
            $whereArr[] = "status = '1'";

        if (!empty($whereArr))
            $where = "AND " . join("AND ", $whereArr);

        $id = $wpdb->escape($groupId);
        $tableName = $wpdb->prefix . "tsc_faq_question";
        $query = "SELECT * FROM {$tableName} WHERE group_id=%d {$where} ORDER BY question_order ASC";

        $results = $wpdb->get_results($wpdb->prepare($query, $id));

        foreach ($results as $result) {
            $q = new Question();
            $q->_Id = $result->id;
            $q->_GroupId = $result->group_id;
            $q->_QuestionOrder = $result->question_order;
            $q->_Question = $result->question;
            $q->_WhoAsked = $result->who_asked;
            $q->_Answer = $result->answer;
            $q->_Status = $result->status;
            $q->_Type = $result->question_type;
            $q->_CreationDate = $result->creation_date;
            $questions[] = $q;
        }

        return $questions;
    }

    public static function searchByGroupId($groupId, $keywords)
    {
        global $wpdb;
        $questions = array();

        $tableName = $wpdb->prefix . "tsc_faq_question";
        $query = "SELECT *, MATCH (question, answer) AGAINST (%s IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION) AS score ";
        $query .= "FROM {$tableName} WHERE group_id=%d ";
        $query .= "AND answer != '' AND status = '1' ";
        $query .= "AND MATCH (question, answer) AGAINST (%s IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION) ";
        $query .= "ORDER BY score DESC";

        $results = $wpdb->get_results($wpdb->prepare($query, $keywords, $groupId, $keywords));

        foreach ($results as $result) {
            $r = array();
            $r["question"] = $result->question;
            $r["answer"] = $result->answer;
            $questions[] = $r;
        }

        return $questions;
    }

    public function save()
    {
        global $wpdb;

        if (!$this->validate())
            return;

        $tableName = $wpdb->prefix . "tsc_faq_question";

        $data = array(
            "group_id" => $this->_GroupId,
            "question_order" => $this->_QuestionOrder,
            "question" => $this->_Question,
            "who_asked" => $this->_WhoAsked,
            "answer" => $this->_Answer,
            "status" => $this->_Status,
            "question_type" => $this->_Type
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

        $tableName = $wpdb->prefix . "tsc_faq_question";

        $query = "DELETE FROM $tableName WHERE id=%d";

        $wpdb->query($wpdb->prepare($query, $this->_Id));
    }
}
