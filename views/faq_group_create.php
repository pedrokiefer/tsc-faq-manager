<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/8/12
 * Time: 1:02 PM
 */
?>
<html>
<body>
<div class="wrap">
    <h2><? echo __("Create Group", 'tsc-faq-manager'); ?></h2>

    <div id="faqMessage" class="bellow-h2"></div>
    <form action="" name="createGroup" method="post">
        <input type="hidden" name="action" value="faqAction"/>
        <input type="hidden" name="req" value="create@Group"/>

        <table class="form-table">
            <tbody>
            <tr>
                <th><? echo __("Group Name", 'tsc-faq-manager'); ?></th>
                <td>
                    <input type="text" name="group_name" value="" class="regular-text"/>

                    <p class="description"><? echo __("The name of the group.", 'tsc-faq-manager'); ?></p>
                </td>
            </tr>
            <tr>
                <th><? echo __("Show Search Box", 'tsc-faq-manager'); ?></th>
                <td>
                    <input type="radio" name="search_box" value="1"/> <? echo __("Yes", 'tsc-faq-manager'); ?> &nbsp;
                    <input type="radio" name="search_box" value="0"/> <? echo __("No", 'tsc-faq-manager'); ?>
                    <p class="description"><? echo __("Place a search box above the group questions.", 'tsc-faq-manager'); ?></p>
                </td>
            </tr>
            <tr>
                <th><? echo __("Show Ask Box", 'tsc-faq-manager'); ?></th>
                <td>
                    <input type="radio" name="ask_box" value="1"/> <? echo __("Yes", 'tsc-faq-manager'); ?> &nbsp;
                    <input type="radio" name="ask_box" value="0"/> <? echo __("No", 'tsc-faq-manager'); ?>
                    <p class="description"><? echo __("Place a submission box below the group questions for users/members to ask questions.", 'tsc-faq-manager'); ?></p>
                </td>
            </tr>

            <tr>
                <th><? echo __("Status", 'tsc-faq-manager'); ?></th>
                <td>
                    <input type="radio" name="status" value="1"/> <? echo __("Active", 'tsc-faq-manager'); ?> &nbsp;
                    <input type="radio" name="status" value="0"/> <? echo __("Inactive", 'tsc-faq-manager'); ?>
                    <p class="description"><? echo __("Deactivating a group will prevent it's questions from being shown on the front-end.", 'tsc-faq-manager'); ?></p>
                </td>
            </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" value="<? echo __("Create", 'tsc-faq-manager'); ?>" class="button-primary" id="submit" name="submit">
        </p>
    </form>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $("form[name=createGroup]").submit(function (e) {
            var form = $(this);
            $.post(ajaxurl, form.serialize(), function (data) {
                alert("data:" + data);
                if (data.status == "error") {
                    jQuery("#faqMessage").removeClass('updated').addClass('error').html("<p>" + data.message + "</p>");
                } else if (data.status == "saved") {
                    tb_remove();
                    self.parent.location.reload();
                }
            }, 'json');
            e.preventDefault();
        });
    });
</script>
</body>
</html>
