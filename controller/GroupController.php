<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/8/12
 * Time: 9:52 AM
 */
require_once(dirname(__FILE__) . "/../models/Group.php");
require_once(dirname(__FILE__) . "/../models/Question.php");

class GroupController
{

    function create()
    {
        global $wpdb;

        if (!$_POST) {
            include(__DIR__ . "/../views/faq_group_create.php");
        } else {
            $group = new Group();

            $group->GroupName = $_POST['group_name'];
            $group->SearchBox = $_POST['search_box'];
            $group->AskBox = $_POST['ask_box'];
            $group->Status = $_POST['status'];

            if (!$group->validate()) {
                echo json_encode(array(
                    "status" => "error",
                    "message" => $group->getValidationMessages()));
                exit();
            }

            $group->save();

            header("Content-type: application/json");
            echo json_encode(array("status" => "saved", "id" => $group->Id));
            exit();
        }
    }

    function edit()
    {
        global $wpdb;

        if (!isset($_REQUEST['id']))
            die(__("Id not defined!", 'tsc-faq-manager'));

        if (!$_POST) {
            $group = Group::load($_REQUEST['id']);
            include(__DIR__ . "/../views/faq_group_edit.php");
        } else {
            $group = Group::load($_REQUEST['id']);

            $group->GroupName = $_POST['group_name'];
            $group->SearchBox = $_POST['search_box'];
            $group->AskBox = $_POST['ask_box'];
            $group->Status = $_POST['status'];

            if (!$group->validate()) {
                echo json_encode(array(
                    "status" => "error",
                    "message" => $group->getValidationMessages()));
                exit();
            }

            $group->save();

            header("Content-type: application/json");
            echo json_encode(array("status" => "saved", "id" => $group->Id));
            exit();
        }
    }

    function editOrder()
    {
        if (!isset($_REQUEST['id']))
            die(__("Id not defined!", 'tsc-faq-manager'));

        if (!$_POST) {
            $group = Group::load($_REQUEST['id']);
            $questions = Question::loadByGroupId($_REQUEST['id']);

            include(__DIR__ . "/../views/faq_group_edit_order.php");
        }
    }

    function saveOrder()
    {
        if (!$_POST)
            return json_encode(array(
                "status" => "error",
                "message" => __("Not a post", 'tsc-faq-manager')));

        if (!isset($_POST['sort_ids']))
            return json_encode(array(
                "status" => "error",
                "message" => __("Invalid Post", 'tsc-faq-manager')));

        $sortOrder = $_POST['sort_ids'];

        foreach ($sortOrder as $order => $id) {
            Question::updateOrderForId($id, $order);
        }

        header("Content-type: application/json");
        echo json_encode(array("status" => "saved"));
    }

    function delete()
    {
        if (!isset($_REQUEST['id']))
            die(__("Id not defined!", 'tsc-faq-manager'));

        $group = Group::load($_REQUEST['id']);
        $group->delete();

        $header = sprintf("Location: %s", $_SERVER['HTTP_REFERER']);
        header($header);
        exit();
    }

}
