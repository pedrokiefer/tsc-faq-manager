<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/8/12
 * Time: 5:42 PM
 */

?>
<html>
<body>
<div class="wrap">
    <h2><? echo __("Create Question", 'tsc-faq-manager'); ?></h2>

    <div id="faqMessage" class="bellow-h2"></div>
    <form action="" name="createQuestion" method="post">
        <input type="hidden" name="action" value="faqAction"/>
        <input type="hidden" name="req" value="create@Question"/>

        <div id="post-body" class="metabox-holder">
            <div id="titlediv">
                <div id="titlewrap">
                    <label for="title" id="title-prompt-text"><? echo __("Enter question here:", 'tsc-faq-manager'); ?></label>
                    <input type="text" id="title" name="question" tabindex="1"/>
                </div>
            </div>

            <div class="postbox" style="background-image: none; background-color: white;">
                <textarea rows="10" class="answerEditor" name="answer" id="answerEditor" style="width: 100%"><p></p>
                </textarea>
            </div>
            <div class="postbox">
                <h3><span><? echo __("Extra Info", 'tsc-faq-manager'); ?></span></h3>

                <div class="inside">
                    <p>
                        <label for="group_id"><? echo __("FAQ Group:", 'tsc-faq-manager'); ?></label>
                        <select name="group_id" id="group_id" style="min-width: 200px;" tabindex="3">
                            <?php
                            foreach ($groups as $g) {
                                echo("<option value=\"". $g->Id ."\">".$g->GroupName."</option>\n");
                            }
                            ?>
                        </select>
                    </p>
                    <p>
                        <label for="group_id"><? echo __("Status:", 'tsc-faq-manager'); ?></label>
                        <input type="radio" name="status" value="1" tabindex="4"/> <? echo __("Active", 'tsc-faq-manager'); ?> &nbsp;
                        <input type="radio" name="status" value="0" tabindex="5"/> <? echo __("Inactive", 'tsc-faq-manager'); ?> &nbsp;
                    </p>
                </div>
            </div>
        </div>

        <p class="submit">
            <input type="submit" value="<? echo __("Create", 'tsc-faq-manager'); ?>" class="button-primary" id="submit" name="submit">
        </p>
    </form>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $("input[name=question]").blur(function () {
            if (this.value == "") {
                $(this).prev('label').fadeIn();
            }
        }).focus(function () {
                $(this).prev('label').fadeOut();
            });

        $("form[name=createQuestion]").submit(function (e) {
            var form = $(this);
            tinyMCE.triggerSave();
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
        tinyMCE.init({
            mode:'exact',
            elements:'answerEditor',
            theme:'advanced',
            skin:'wp_theme',
            language: <? echo '"' . (('' == get_locale()) ? 'en' : strtolower(substr(get_locale(), 0, 2))) . '"'; ?>,
            theme_advanced_toolbar_location:"top",
            theme_advanced_toolbar_align:"left",
            theme_advanced_statusbar_location:"bottom",
            theme_advanced_resizing:true,
            theme_advanced_resize_horizontal:false,
            formats:{
                alignleft:[
                    {selector:'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles:{textAlign:'left'}},
                    {selector:'img,table', classes:'alignleft'}
                ],
                aligncenter:[
                    {selector:'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles:{textAlign:'center'}},
                    {selector:'img,table', classes:'aligncenter'}
                ],
                alignright:[
                    {selector:'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles:{textAlign:'right'}},
                    {selector:'img,table', classes:'alignright'}
                ],
                strikethrough:{inline:'del'}
            },
            theme_advanced_buttons1:"bold,italic,strikethrough,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,|,link,unlink",
            theme_advanced_buttons2:"",
            theme_advanced_buttons3:"",
            theme_advanced_buttons4:""
        })
    });
</script>
</body>
</html>
