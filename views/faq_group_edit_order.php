<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/16/12
 * Time: 4:02 PM
 */
?>
<div class="wrap">
    <h2><? echo __("Order Questions", 'tsc-faq-manager'); ?></h2>

    <div id="faqMessage" class="bellow-h2"></div>
    <form action="" name="editOrder" method="post">
        <input type="hidden" name="action" value="faqAction"/>
        <input type="hidden" name="req" value="saveOrder@Group"/>
        <input type="hidden" name="id" value="<? echo $group->Id; ?>" />

        <ul id="sortable">
            <?php
            foreach ($questions as $q) {
                echo("<li id=\"sort_" . $q->Id . "\" class=\"ui-state-default\">");
                echo("<span class=\"ui-icon ui-icon-arrowthick-2-n-s\"></span>");
                echo("<input type=\"hidden\" name=\"sort_ids[]\" value=\"".$q->Id."\"/>");
                echo($q->Question . "</li>\n");
            }
            ?>
        </ul>
        <p class="submit">
            <input type="submit" value="<? echo __("Save", 'tsc-faq-manager'); ?>" class="button-primary" id="submit" name="submit">
        </p>
    </form>
</div>
<style>
    #sortable {
        list-style-type: none;
        margin: 0;
        padding: 0;
        width: 100%;
        cursor: ns-resize;
    }

    #sortable li {
        margin: 0 3px 3px 3px;
        padding: 0.4em;
        padding-left: 1.5em;
        font-size: 13px;
    }

    #sortable li span {
        position: absolute;
        margin-left: -1.3em;
    }
</style>
<script>
    jQuery(document).ready(function ($) {
        $("#sortable").sortable();
        $("#sortable").disableSelection();
        $("form[name=editOrder]").submit(function (e) {
            var form = $(this);
            $.post(ajaxurl, form.serialize(), function (data) {
                if (data.status == "error") {
                    jQuery("#faqMessage").removeClass('updated').addClass('error').html("<p>" + data.message + "</p>");
                } else if (data.status == "saved") {
                    tb_remove();
                }
            }, 'json');
            e.preventDefault();
        });
    });
</script>