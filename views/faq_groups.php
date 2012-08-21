<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/7/12
 * Time: 2:32 PM
 */
?>
<div class="wrap">
    <div id="icon-edit" class="icon32 icon32-posts-post"><br/></div>
    <h2>FAQ - <? echo __("Groups", 'tsc-faq-manager'); ?> <a class="thickbox add-new-h2"
                        href="<? echo admin_url('admin-ajax.php') . "?action=faqAction&req=create@Group&width=500&height=400"; ?>"
                        title="Create Group"><? echo __("Add New", 'tsc-faq-manager'); ?></a></h2>

    <?php
    $listing->display();
    ?>
</div>