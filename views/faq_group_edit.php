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
    <h2>Edit Group</h2>

    <div id="faqMessage" class="bellow-h2"></div>
    <form action="" name="editGroup" method="post">
        <input type="hidden" name="action" value="faqAction"/>
        <input type="hidden" name="req" value="edit@Group"/>
        <input type="hidden" name="id" value="<? echo $group->Id; ?>" />

        <table class="form-table">
            <tbody>
            <tr>
                <th>Group Name</th>
                <td>
                    <input type="text" name="group_name" value="<? echo $group->GroupName; ?>" class="regular-text"/>

                    <p class="description">The name of the group.</p>
                </td>
            </tr>
            <tr>
                <th>Show Search Box</th>
                <td>
                    <input type="radio" name="search_box"
                           value="1"<?php echo $group->SearchBox ? ' checked="checked"' : ''; ?> /> Yes &nbsp;
                    <input type="radio" name="search_box"
                           value="0"<?php echo $group->SearchBox ? '' : ' checked="checked"'; ?> /> No
                    <p class="description">Place a search box above the group questions.</p>
                </td>
            </tr>
            <tr>
                <th>Show Ask Box</th>
                <td>
                    <input type="radio" name="ask_box" value="1"<?php echo $group->AskBox ? ' checked="checked"' : ''; ?> />
                    Yes &nbsp;
                    <input type="radio" name="ask_box" value="0"<?php echo $group->AskBox ? '' : ' checked="checked"'; ?> />
                    No
                    <p class="description">Place a submission box below the group questions for users/members to ask
                        questions</p>
                </td>
            </tr>

            <tr>
                <th>Status</th>
                <td>
                    <input type="radio" name="status" value="1"<?php echo $group->Status ? ' checked="checked"' : ''; ?> />
                    Active &nbsp;
                    <input type="radio" name="status" value="0"<?php echo $group->Status ? '' : ' checked="checked"'; ?> />
                    Inactive
                    <p class="description">Deactivating a group will prevent it's questions from being shown on the
                        front-end.</p>
                </td>
            </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" value="Save" class="button-primary" id="submit" name="submit">
        </p>
    </form>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $("form[name=editGroup]").submit(function (e) {
            var form = $(this);
            $.post(ajaxurl, form.serialize(), function (data) {
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
