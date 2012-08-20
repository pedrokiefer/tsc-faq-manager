<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/20/12
 * Time: 6:28 PM
 */
class MailHelper
{
    public static function sendNotifyEmail($from, $group, $question)
    {
        global $tscfm;

        $subject = "New FAQ Question Asked @" . get_bloginfo('name');
        $message = "<div style=\"width: 600px; margin: 0 auto; font-family: Arial; font-size:10pt; color:#888\">";
        $message .= "<h2 style=\"font-family: Arial; font-size:18pt; color:#888; border-bottom: 1px solid #999\">New FAQ Question</h2>";
        $message .= "<p><strong>From</strong>: {$from}</p>";
        $message .= "<p><strong>Question Group</strong>: {$group->GroupName}</p>";
        $message .= "<p><strong>Question</strong>:</p>";
        $message .= "<p>{$question}</p>";
        $message .= "</div>";

        wp_mail($tscfm->settings->EmailAddress, $subject, $message);
    }

    public static function sendReplyEmail($question)
    {
        $subject = "FAQ Question @" . get_bloginfo('name');
        $message = "<div style=\"width: 600px; margin: 0 auto; font-family: Arial; font-size:10pt; color:#888\">";
        $message .= "<h2 style=\"font-family: Arial; font-size:18pt; color:#888; border-bottom: 1px solid #999\">Answered!</h2>";
        $message .= "<p>Your FAQ question was answered!</p>";
        $message .= "<p><strong>Question</strong>:</p>";
        $message .= "<p>{$question->Question}</p>";
        $message .= "<p><strong>Answer</strong>:</p>";
        $message .= "<p>{$question->Answer}</p>";
        $message .= "</div>";

        wp_mail($question->WhoAsked, $subject, $message);
    }
}
