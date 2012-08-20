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
    <h2>Edit Question</h2>

    <div id="faqMessage" class="bellow-h2"></div>
    <form action="" name="editQuestion" method="post">
        <input type="hidden" name="action" value="faqAction"/>
        <input type="hidden" name="req" value="edit@Question"/>
        <input type="hidden" name="id" value="<? echo $question->Id; ?>" />

        <div id="post-body" class="metabox-holder">
            <div id="titlediv">
                <div id="titlewrap">
                    <label for="title" id="title-prompt-text">Enter question here:</label>
                    <input type="text" id="title" name="question" value="<? echo $question->Question; ?>" tabindex="1"/>
                </div>
            </div>

            <div class="postbox" style="background-image: none; background-color: white;">
                <textarea rows="10" class="answerEditor" name="answer" id="answerEditor" style="width: 100%"><? echo $question->Answer; ?>
                </textarea>
            </div>
            <div class="postbox">
                <h3><span>Extra Info</span></h3>

                <div class="inside">
                    <p>
                        <label for="group_id">FAQ Group:</label>
                        <select name="group_id" id="group_id" style="min-width: 200px;" tabindex="3">
                            <?php
                            foreach ($groups as $g) {
                                $selected = "";
                                if ($g->Id == $question->GroupId)
                                    $selected = " selected=\"selected\"";

                                echo("<option value=\"". $g->Id ."\"$selected>".$g->GroupName."</option>\n");
                            }
                            ?>
                        </select>
                    </p>
                    <p>
                        <label for="group_id">Status:</label>
                        <input type="radio" name="status" value="1"<?php echo $question->Status ? ' checked="checked"' : ''; ?> tabindex="4"/> Active &nbsp;
                        <input type="radio" name="status" value="0"<?php echo $question->Status ? '' : ' checked="checked"'; ?> tabindex="5"/> Inactive &nbsp;
                    </p>
                </div>
            </div>
        </div>

        <p class="submit">
            <input type="submit" value="Save" class="button-primary" id="submit" name="submit">
        </p>
    </form>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var q = $("input[name=question]");
        if (q.val != "")
            q.prev('label').hide();

        $("input[name=question]").blur(function () {
            if (this.value == "") {
                $(this).prev('label').fadeIn();
            }
        }).focus(function () {
                $(this).prev('label').fadeOut();
            });

        $("form[name=editQuestion]").submit(function (e) {
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
