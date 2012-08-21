<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/7/12
 * Time: 2:32 PM
 */
?>
<div class="wrap">
    <div id="icon-options-general" class="icon32 icon32-posts-post"><br/></div>
    <h2>FAQ - <? echo __("Settings", 'tsc-faq-manager'); ?></h2>

    <div id="faqMessage" class="bellow-h2"></div>
    <form action="" name="settings" method="post">
        <input type="hidden" name="action" value="faqAction"/>
        <input type="hidden" name="req" value="save@Settings"/>

        <h3><? echo __("New Questions", 'tsc-faq-manager'); ?></h3>

        <table class="form-table">
            <tbody>
            <th scope="row"><label for="enable_questions"><? echo __("Enable New Questions", 'tsc-faq-manager'); ?></label></th>
            <td>
                <input type="checkbox" name="enable_questions" id="enable_questions" <? echo $settings->EnableQuestions ? "checked=\"checked\"" : "" ?>/>
                    <span class="description"><? echo __("Enable questions on the front-end.", 'tsc-faq-manager'); ?></span>
            </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="email_address"><? echo __("Notification Address", 'tsc-faq-manager'); ?></label></th>
                <td>
                    <input type="text" name="email_address" id="email_address" value="<? echo $settings->EmailAddress ?>" class="regular-text"/>
                    <span class="description"><? echo __("Email address to were the notification will be sent.", 'tsc-faq-manager'); ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="email_notify"><? echo __("Notify on New Questions", 'tsc-faq-manager'); ?></label></th>
                <td>
                    <input type="checkbox" name="email_notify" id="email_notify" <? echo $settings->EmailNotify ? "checked=\"checked\"" : "" ?>/>
                    <span class="description"><? echo __("Get a notification email when a new question is posted on the site.", 'tsc-faq-manager'); ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="notify_on_answer"><? echo __("Notify Enquirer When Answered", 'tsc-faq-manager'); ?></label></th>
                <td>
                    <input type="checkbox" name="notify_on_answer" id="notify_on_answer" <? echo $settings->NotifyOnAnswer ? "checked=\"checked\"" : "" ?>/>
                    <span class="description"><? echo __("Send an email to the person that posted the question.", 'tsc-faq-manager'); ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="save_emails_addresses"><? echo __("Require Email Address", 'tsc-faq-manager'); ?></label></th>
                <td>
                    <input type="checkbox" name="save_emails_addresses" id="save_emails_addresses" <? echo $settings->SaveEmailsAddresses ? "checked=\"checked\"" : "" ?>/>
                    <span class="description"><? echo __("To ask a question, the person needs a valid email address.", 'tsc-faq-manager'); ?></span>
                </td>
            </tr>
            </tbody>
        </table>

        <h3><? echo __("Shortcode Skin", 'tsc-faq-manager'); ?></h3>
        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row"><label for="skin"><? echo __("Current Skin", 'tsc-faq-manager'); ?></label></th>
                <td>
                    <select name="skin" id="skin">
                        <?php
                        foreach($skinsList as $skinName => $skin) {
                            $selected = "";
                            if (strcmp($settings->Skin, $skinName) == 0)
                                $selected .= "selected=\"selected\"";
                            echo("<option value=\"". $skinName ."\"$selected>".$skin['Name']."</option>");
                        }
                        ?>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button-primary" value="<? echo __("Save Changes", 'tsc-faq-manager'); ?>"/>
        </p>
    </form>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $("form[name=settings]").submit(function (e) {
            var form = $(this);
            $.post(ajaxurl, form.serialize(), function(data) {
                if (data.status == "error") {
                    var message = "";
                    if (data.message.Exception) {
                        message = data.message.Exception;
                    } else {
                        message = data.message;
                    }
                    $("#faqMessage").removeClass("updated").addClass("error").html("<p>"+ message + "</p>").show().fadeOut(2600);
                } else if (data.status == "saved") {
                    $("#faqMessage").removeClass("error").addClass("updated").html("Changes saved!").show().fadeOut(2600);
                }
            });
            e.preventDefault();
        });
    });
</script>
