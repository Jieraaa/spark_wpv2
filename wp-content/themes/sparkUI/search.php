<?php
    if (!is_user_logged_in()) {
    wp_redirect( home_url().'/wp-login.php' );
    }
    get_header(); ?>
<div class="container" style="margin-top: 10px">
    <div class="row" style="width: 100%">
        <div class="col-md-9 col-sm-9 col-xs-12" id="col9">
<!--        --><?php //require "template/qa/QA_search.php";?>
            <?php require "template/search-template.php";?>
        </div>
        <?php get_sidebar();?>
    </div>
</div>
<?php get_footer(); ?>
