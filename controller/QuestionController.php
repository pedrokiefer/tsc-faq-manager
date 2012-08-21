<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/8/12
 * Time: 5:05 PM
 */

require_once(dirname(__FILE__) . "/../models/Group.php");
require_once(dirname(__FILE__) . "/../models/Question.php");
require_once(dirname(__FILE__) . "/MailHelper.php");

class QuestionController
{

    function create()
    {
        global $wpdb;

        if (!$_POST) {
            $groups = Group::loadAll();
            include(__DIR__ . "/../views/faq_question_create.php");
        } else {
            $question = new Question();

            $question->Question = $_POST['question'];
            $question->Answer = $_POST['answer'];
            $question->GroupId = $_POST['group_id'];
            $question->Status = $_POST['status'];

            if (!$question->validate()) {
                echo json_encode(array(
                    "status" => "error",
                    "message" => $question->getValidationMessages()));
                exit();
            }

            $question->save();

            header("Content-type: application/json");
            echo json_encode(array("status" => "saved", "id" => $question->Id));
            exit();
        }
    }

    function edit()
    {
        global $wpdb, $tscfm;

        if (!isset($_REQUEST['id']))
            die("Id not defined!");

        if (!$_POST) {
            $question = Question::load($_REQUEST['id']);
            $groups = Group::loadAll();
            include(__DIR__ . "/../views/faq_question_edit.php");
        } else {
            $question = Question::load($_REQUEST['id']);
            $sendMail = false;

            if ($question->Type == 1) {
                if (!empty($_POST['answer']) && $_POST['status'] != 0) {
                    $question->Type = 2;
                    $sendMail = true;
                }
            }

            $question->Question = $_POST['question'];
            $question->Answer = $_POST['answer'];
            $question->GroupId = $_POST['group_id'];
            $question->Status = $_POST['status'];

            if (!$question->validate()) {
                echo json_encode(array(
                    "status" => "error",
                    "message" => $question->getValidationMessages()));
                exit();
            }

            $question->save();

            if (($tscfm->settings->NotifyOnAnswer == "1") && ($sendMail == true)) {
                MailHelper::sendReplyEmail($question);
            }


            header("Content-type: application/json");
            echo json_encode(array("status" => "saved", "id" => $question->Id));
            exit();
        }
    }

    function delete()
    {
        if (!isset($_REQUEST['id']))
            die("Id not defined!");

        $question = Question::load($_REQUEST['id']);
        $question->delete();

        $header = sprintf("Location: %s", $_SERVER['HTTP_REFERER']);
        header($header);
        exit();
    }

}
