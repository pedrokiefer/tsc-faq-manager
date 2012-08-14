<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/8/12
 * Time: 5:05 PM
 */

require_once(dirname(__FILE__) . "/../models/QuestionModel.php");

class QuestionController
{

    function create()
    {
        global $wpdb;

        if (!$_POST) {
            include(__DIR__ . "/../views/faq_question_create.php");
        } else {
            $question = new QuestionModel();

            $question->Question = $wpdb->escape($_POST['question']);
            $question->Answer = $wpdb->escape($_POST['answer']);
            $question->GroupId = $wpdb->escape($_POST['group_id']);
            $question->Status = $wpdb->escape($_POST['status']);

            if (!$question->validate()) {
                echo json_encode(array(
                    "status" => "error",
                    "message" => $question->getValidationMessages()));
                exit();
            }

            $question->save();

            echo json_encode(array("status" => "saved", "id" => $question->Id));
            exit();
        }
    }

    function edit()
    {
        global $wpdb;

        if (!isset($_REQUEST['id']))
            die("Id not defined!");

        if (!$_POST) {
            if (!isset($_REQUEST['id']))
                die("Id not defined!");

            $question = QuestionModel::load($_REQUEST['id']);
            include(__DIR__ . "/../views/faq_question_edit.php");
        } else {
            $question = QuestionModel::load($_REQUEST['id']);

            $question->Question = $wpdb->escape($_POST['question']);
            $question->Answer = $wpdb->escape($_POST['answer']);
            $question->GroupId = $wpdb->escape($_POST['group_id']);
            $question->Status = $wpdb->escape($_POST['status']);

            if (!$question->validate()) {
                echo json_encode(array(
                    "status" => "error",
                    "message" => $question->getValidationMessages()));
                exit();
            }

            $question->save();

            echo json_encode(array("status" => "saved", "id" => $question->Id));
            exit();
        }
    }

    function delete()
    {
        if (!isset($_REQUEST['id']))
            die("Id not defined!");

        $question = QuestionModel::load($_REQUEST['id']);
        $question->delete();

        $header = sprintf("Location: %s", $_SERVER['HTTP_REFERER']);
        header($header);
        exit();
    }

}
