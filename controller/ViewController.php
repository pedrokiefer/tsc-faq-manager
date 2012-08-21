<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/20/12
 * Time: 1:30 PM
 */

require_once(dirname(__FILE__) . "/../models/Group.php");
require_once(dirname(__FILE__) . "/../models/Question.php");
require_once(dirname(__FILE__) . "/MailHelper.php");

class ViewController
{

    function searchQuestion()
    {
        if (!isset($_POST['groupId']))
            die();

        if (empty($_POST['faq_query']) || $_POST['faq_query'] == "") {
            die();
        }

        $questions = Question::searchByGroupId($_POST['groupId'], $_POST['faq_query']);
        $result = array("status" => "success", "result" => $questions);

        header("Content-type: application/json");
        echo json_encode($result);
    }

    function addNewQuestion()
    {
        global $tscfm;

        if (!isset($_POST['groupId']))
            die();

        if (empty($_POST["email-address"]) || empty($_POST["new-question"]))
            die();

        $email = is_email($_POST["email-address"]);
        if (!$email) {
            header("Content-type: application/json");
            echo json_encode(array("status" => "error", "message" => __("Invalid Email Address", 'tsc-faq-manager')));
            die();
        }

        $questionStr = sanitize_text_field($_POST["new-question"]);
        $group = Group::load($_POST['groupId']);

        $question = new Question();
        $question->Question = $questionStr;
        $question->WhoAsked = $email;
        $question->Status = 0;
        $question->GroupId = $group->Id;
        $question->Type = 1;
        $question->save();

        if ($tscfm->settings->EmailNotify == "1") {
            MailHelper::sendNotifyEmail($email, $group, $questionStr);
        }

        $result = array("status" => "success");

        header("Content-type: application/json");
        echo json_encode($result);
    }
}
