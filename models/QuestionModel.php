<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/7/12
 * Time: 1:40 PM
 */
require_once("TscModel.php");

class QuestionModel extends TscModel
{

    protected $_Id = NULL;
    protected $_GroupId;
    protected $_QuestionOrder = 0;
    protected $_Question;
    protected $_WhoAsked;
    protected $_Answer;
    protected $_Status;
    protected $_CreationDate;

    public static $questionTable = "CREATE TABLE %s (
        id int(11) NOT NULL AUTO_INCREMENT,
        group_id int(11) NOT NULL DEFAULT 0,
        question_order int(11) NOT NULL DEFAULT 0,
        question varchar(1024) NOT NULL,
        who_asked varchar(512) NOT NULL,
        answer text NOT NULL,
        status tinyint(1) NOT NULL DEFAULT 1,
        creation_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY group_id (group_id),
        KEY question_order (question_order),
        KEY status (status)
        )";

    public function validate()
    {
        $valid = true;

        if (!$this->_Question || empty($this->_Question)) {
            $this->validationMessages .= "Missing Question <br/>";
            $valid = false;
        }

        if (!$this->_GroupId || empty($this->_GroupId)) {
            $this->validationMessages .= "Missing Group <br/>";
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

        $question = new QuestionModel();
        $question->_Id = $result->id;
        $question->_GroupId = $result->group_id;
        $question->_QuestionOrder = $result->question_order;
        $question->_Question = $result->question;
        $question->_WhoAsked = $result->who_asked;
        $question->_Answer = $result->answer;
        $question->_Status = $result->status;
        $question->_CreationDate = $result->creation_date;

        return $question;
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

        $tableName = $wpdb->prefix . "tsc_faq_question";

        $query = "DELETE FROM $tableName WHERE id=%d";

        $wpdb->query($wpdb->prepare($query, $this->_Id));
    }
}
