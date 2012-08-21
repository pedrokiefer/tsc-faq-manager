<?php
/*
Skin Name: Tecnosenior FAQ Skin
Description: Tecnosenior FAQ Skin
Version: 1.0
Author: Pedro Kiefer
Author URI: http://www.pedro.kiefer.com.br
 */

/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/20/12
 * Time: 10:27 AM
 */

function tsc_skin_get_headers()
{
    $headers = array(
        "js" => array(
            "jquery",
            "jquery-ui",
            array("name" => "tecnosenior-faq", "file" => plugin_dir_url(__FILE__) . "/tecnosenior-faq.js")
        ),
        "css" => array("tecnosenior-faq" => plugin_dir_url(__FILE__) . "/tecnosenior-faq.css")
    );

    return $headers;
}

function tsc_skin_render($group, $questions)
{
    $html = "";
    if ($group->SearchBox) {
        $html .= render_search_box($group->Id);
    }

    $i = 0;
    $html .= "<div class=\"span-14 prepend-1 append-1 last faq-questions\" id=\"faq-questions-list\">\n";
    foreach ($questions as $q) {
        $html .= render_question($q, ($i % 2 == 1));
        $i++;
    }
    $html .= "</div>";

    if ($group->AskBox) {
        $html .= render_ask_box($group->Id);
    }

    return $html;
}

function render_search_box($groupId)
{
    $html = "";
    $html .= "<div class=\"span-14 prepend-1 append-1 last faq-search-box\">";
    $html .= "<form action=\"\" name=\"search\" method=\"post\">";
    $html .= "<input type=\"hidden\" name=\"action\" value=\"faqQuery\"/>";
    $html .= "<input type=\"hidden\" name=\"req\" value=\"search\"/>";
    $html .= "<input type=\"hidden\" name=\"groupId\" value=\"{$groupId}\"/>";
    $html .= "    <label for=\"faq_query\" class=\"faq-search\">Palavra Chave</label>";
    $html .= "    <div class=\"faq-search-field\">";
    $html .= "        <input type=\"text\" name=\"faq_query\" id=\"faq_query\" value=\"\"/>";
    $html .= "        <img id=\"faq-magnifier\" src=\"" . plugin_dir_url(__FILE__) . "faq-magnifier.png\" width=\"22\" height=\"28\">";
    $html .= "    </div>";
    $html .= "</form>";
    $html .= "</div>";

    return $html;
}

function render_question($question, $even)
{
    $evenStr = $even ? "even" : "odd";

    $html = "<div class=\"faq-question\">";
    $html .= "<div class=\"question-head " . $evenStr . "\">";
    $html .= "    <h2><a href=\"#\">" . $question->Question . "</a></h2>";
    $html .= "</div>";
    $html .= "<div class=\"question-body\">";
    $html .= $question->Answer;
    $html .= "</div>";
    $html .= "</div>";

    return $html;
}

function render_ask_box($groupId)
{

    $html = "<div class=\"span-14 prepend-1 append-1 last faq-ask-question\">";
    $html .= "    <h2>Envie sua pergunta!</h2>";
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
    $html .= "            <label for=\"new-question\">D&uacute;vida</label>";
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