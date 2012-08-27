<?php
/*
Skin Name: Default Skin
Description: Default sample skin for tsc-faq-manager
Version: 1.0
Author: Pedro Kiefer
Author URI: http://www.pedro.kiefer.com.br
 */

/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/15/12
 * Time: 2:44 PM
 */

function tsc_skin_get_headers()
{
    $headers = array(
        "js" => array("jquery",
            array("name" => "tecnosenior-faq", "file" => plugin_dir_url(__FILE__) . "/faq-default.js")),
        "css" => array("faq-default" => plugin_dir_url(__FILE__) . "/default.css")
    );

    return $headers;
}

function tsc_skin_render($group, $questions)
{
    $html = "<div class=\"faq\">";
    if ($group->SearchBox) {
        $html .= render_search_box($group->Id);
    }

    $html .= "<dl class=\"faq-questions\" id=\"faq-questions-list\">";
    foreach ($questions as $q) {
        $html .= render_question($q);
    }
    $html .= "</dl>";

    if ($group->AskBox) {
        $html .= render_ask_box($group->Id);
    }
    $html .= "</div>";

    return $html;
}

function render_search_box($groupId)
{
    $html = "<div class=\"faq-search-box\">";
    $html .= "<form action=\"\" name=\"search\" method=\"post\">";
    $html .= "<input type=\"hidden\" name=\"action\" value=\"faqQuery\"/>";
    $html .= "<input type=\"hidden\" name=\"req\" value=\"search\"/>";
    $html .= "<input type=\"hidden\" name=\"groupId\" value=\"{$groupId}\"/>";
    $html .= "    <label for=\"faq_query\" class=\"faq-search\">Palavra Chave</label>";
    $html .= "        <input type=\"text\" name=\"faq_query\" id=\"faq_query\" value=\"\"/>";
    $html .= "</form>";
    $html .= "</div>";

    return $html;
}

function render_question($question)
{
    $html = "<dt>" . $question->Question . "</dt>";
    $html .= "<dd>" . $question->Answer . "</dd>";

    return $html;
}

function render_ask_box($groupId)
{
    $html = "<div class=\"faq-ask-question\">";
    $html .= "<form action=\"\" name=\"ask-question\" method=\"post\">";
    $html .= "<input type=\"hidden\" name=\"action\" value=\"faqQuery\"/>";
    $html .= "<input type=\"hidden\" name=\"req\" value=\"question\"/>";
    $html .= "<input type=\"hidden\" name=\"groupId\" value=\"{$groupId}\"/>";
    $html .= "    <fieldset class=\"faq-form\">";
    $html .= "        <div>";
    $html .= "            <label for=\"email-address\">Email</label>";
    $html .= "            <input type=\"text\" id=\"email-address\" name=\"email-address\"/>";
    $html .= "        </div>";
    $html .= "        <div>";
    $html .= "            <label for=\"new-question\">Question</label>";
    $html .= "            <textarea id=\"new-question\" name=\"new-question\"></textarea>";
    $html .= "        </div>";
    $html .= "        <div>";
    $html .= "            <input type=\"submit\" id=\"submit\" name=\"submit\" class=\"faq-form-button\" value=\"Enviar\">";
    $html .= "        </div>";
    $html .= "    </fieldset>";
    $html .= "</form>";
    $html .= "</div>";

    return $html;
}