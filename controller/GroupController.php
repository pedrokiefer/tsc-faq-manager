<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/8/12
 * Time: 9:52 AM
 */
require_once(dirname(__FILE__) . "/../models/GroupModel.php");

class GroupController
{

    function create()
    {
        global $wpdb;

        if (!$_POST) {
            include(__DIR__ . "/../views/faq_group_create.php");
        } else {
            $group = new GroupModel();

            $group->GroupName = $wpdb->escape($_POST['group_name']);
            $group->SearchBox = $wpdb->escape($_POST['search_box']);
            $group->AskBox = $wpdb->escape($_POST['ask_box']);
            $group->Status = $wpdb->escape($_POST['status']);

            if (!$group->validate()) {
                echo json_encode(array(
                    "status" => "error",
                    "message" => $group->getValidationMessages()));
                exit();
            }

            $group->save();

            echo json_encode(array("status" => "saved", "id" => $group->Id));
            exit();
        }
    }

    function edit()
    {
        global $wpdb;

        if (!isset($_REQUEST['id']))
            die("Id not defined!");

        if (!$_POST) {
            $group = GroupModel::load($_REQUEST['id']);
            include(__DIR__ . "/../views/faq_group_edit.php");
        } else {
            $group = GroupModel::load($_REQUEST['id']);

            $group->GroupName = $wpdb->escape($_POST['group_name']);
            $group->SearchBox = $wpdb->escape($_POST['search_box']);
            $group->AskBox = $wpdb->escape($_POST['ask_box']);
            $group->Status = $wpdb->escape($_POST['status']);

            if (!$group->validate()) {
                echo json_encode(array(
                    "status" => "error",
                    "message" => $group->getValidationMessages()));
                exit();
            }

            $group->save();

            echo json_encode(array("status" => "saved", "id" => $group->Id));
            exit();
        }
    }

    function delete()
    {
        if (!isset($_REQUEST['id']))
            die("Id not defined!");

        $group = GroupModel::load($_REQUEST['id']);
        $group->delete();

        $header = sprintf("Location: %s", $_SERVER['HTTP_REFERER']);
        header($header);
        exit();
    }

}
