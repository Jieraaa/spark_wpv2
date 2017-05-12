<?php
/**单独的文章模板
 * Created by PhpStorm.
 * User: Bless
 * Date: 2017/4/7
 * Time: 16:52
 * 第58行需手动修改pageID为My Post ID 待修改
 */
 get_header(); ?>
<?php

wp_enqueue_script('fep-script');
wp_enqueue_media();

$current_user = wp_get_current_user();
$author_posts = new WP_Query(array('posts_per_page' => $per_page, 'paged' => $paged, 'orderby' => 'DESC', 'author' => $current_user->ID, 'post_status' => $status,'category_name'=>'project'));
$post_id = get_the_ID();

//埋数据点
session_start();
$_SESSION['post_id'] = get_the_ID();
$_SESSION['post_type'] =get_post_type(get_the_ID());
$_SESSION['user_id'] = get_current_user_id();
$_SESSION['timestamp'] = date("Y-m-d H:i:s",time() + 8*3600);
writeUserTrack();
?>

<div class="container" style="margin-top: 10px">
        <div class="row" style="width: 100%">
            <div class="col-md-9 col-sm-9 col-xs-9" id="col9">
                <!--引入动态模板-->
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post();?>
                <!--    文章内容-->
                 <h2><b><?php the_title(); ?></b></h2><hr>
                <?php the_content(); ?><hr>
                <?php comments_template(); ?>
                <?php endwhile;?>
                <?php else: ?>
                <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>

                <?php endif; ?>

            </div>
            <div class="col-md-3 col-sm-3 col-xs-3 right" id="col3">
                <style type="text/css">
                    .mulu a{
                        display: block;
                        border: #9ea7af 1px solid;
                        height: 50px;
                        padding-left: 30px;
                    }
                    .mulu .mulu_item{
                        display: block;
                        border: #9ea7af 1px solid;
                        height: 50px;
                        padding-left: 50px;
                    }

                </style>

                <!--判断用户是否为项目发布者，若是，则显示编辑按钮-->
                <?php global $current_user;
                get_currentuserinfo();
                $current_user->user_login  ;
                ?>
                <?php if($current_user->user_login  == get_the_author()) {
                    require 'template/project/project_edit_button.php';
                }else {
                    require 'template/project/project_release_button.php';
                }
                ?>



<!--                <div class="wiki_sidebar_wrap">-->
<!--                    <div class="list-group mulu">-->
<!--                        <a href="#" class="list-group-item">-->
<!--                            <span class="glyphicon glyphicon-align-left" aria-hidden="true"></span>-->
<!--                            目录-->
<!--                        </a>-->
<!--                        --><?php
//                        global $post;
//                        $wiki_content = $post->post_content;
//                        $regex = "/\<h(?:.*)\>/";
//                        $match = array();
//                        preg_match_all($regex, $wiki_content, $match);
//                        for($i=0;$i<count($match[0]);$i++) {
//                            $wiki_title_item = trim($match[0][$i]);
//                            $wiki_format_title = substr($wiki_title_item,4,-5);
//                            if(empty($wiki_format_title)) {
//                                continue;
//                            }
//                            ?>
<!--                            <a href="#" class="list-group-item mulu_item">--><?php //echo $wiki_format_title; ?><!--</a>-->
<!--                            --><?php
//                        }
//                        ?>
<!--                    </div>-->
<!--                </div>-->
                <div class="sidebar-grey-frame" style="margin-top: 30px">
                    <p>发布者：</p>
                    <span id="" ><?php the_author(); ?></span><br>
                    <p>分类：</p>
                    <span id="" ><?php the_category(', ') ?></span><br>
                    <p>标签：</p>
                    <span id=""><?php the_tags("", "  ",''); ?></span><br>
                    <p>更新：</p>
                    <span id="" ><?php the_modified_time('Y年n月j日 h:m:s'); ?></span><br>
                    <p>浏览：</p>
                    <span id=""><?php setProjectViews(get_the_ID()); ?><?php echo getProjectViews(get_the_ID()); ?> 次</span><br>
                    <p>评论：</p>
                    <span><?php comments_popup_link('0 条', '1 条', '% 条', '', '评论已关闭'); ?></span><br>
                </div><br>

                <?php $related_wiki = writeProWiki(get_the_ID());?>
                <div class="related_wikis">
                        <div class="sidebar_list_header">
                            <p>相关知识</p>
                            <a id="sidebar_list_link" onclick="show_more_wiki()">更多</a>
                        </div>
                        <!--分割线-->
                        <div style="height: 2px;background-color: lightgray"></div>
                        <div class="related_wiki" id="related_wiki">
                            <ul style="padding-left: 20px; ">
                                <?php
                                //控制条数
                                if(sizeof($related_wiki)<5){$length = sizeof($related_wiki);}
                                else{$length = 5;}
                                for($i=0;$i<$length;$i++){ ?>
                                    <li class="list-group-item">
                                        <a href="<?php echo get_permalink($related_wiki[$i]["wiki_id"]);?>" class="question-title">
                                            <?php echo get_the_title($related_wiki[$i]["wiki_id"]);?>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>

                        <div class="more_related_wiki" id="more_related_wiki" style="display: none">
                            <ul style="padding-left: 20px">
                                <?php
                                //控制条数
                                if(sizeof($related_wiki)>=15){$length = 15;}
                                else{$length = sizeof($related_wiki);}

                                for($i=0;$i<$length;$i++){ ?>
                                    <li class="list-group-item">
                                        <a href="<?php echo get_permalink($related_wiki[$i]["wiki_id"]);?>" class="question-title" id="more_wiki">
                                            <?php echo get_the_title($related_wiki[$i]["wiki_id"]);?>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                </div>

                <?php
                $current_url = curPageURL();
                $url_array=parse_url($current_url);
                $current_page_id=explode("=",$url_array['query'])[1];
                ?>
                <?php if(is_user_logged_in()){ ?>
                    <div class="sidebar_button" id="ask_button">
                        <?php session_start();
                            $_SESSION['post_id'] = $current_page_id;
                            $_SESSION['post_type'] = get_post_type($current_page_id);?>
                        <a onclick="addLayer()" id="ask_link">我要提问</a>
                    </div>
                <?php }else{ ?>
                    <div class="sidebar_button" id="ask_button">
                        <a href="<?php echo wp_login_url( get_permalink($current_page_id) ); ?>">我要提问</a>
                    </div>
                <?php } ?>


            </div>
            <?php //get_sidebar();?>
        </div>
    </div>

<?php get_footer(); ?>
<script>
    var flag=false;
    function show_more_wiki() {
        var related_wiki=document.getElementById('related_wiki');
        var more_related_wiki = document.getElementById('more_related_wiki');
        if(flag){
            related_wiki.style.display ="block";
            more_related_wiki.style.display="none";
        }else{
            related_wiki.style.display="none";
            more_related_wiki.style.display="block";
        }
        flag=!flag;
    }
    function addLayer() {
        layer.open({
            type : 2,
            title: "提问", //不显示title   //'layer iframe',
            content: '<?php echo site_url().get_page_address('ask_tiny');?>', //iframe的url
            area: ['70%', '80%'],
            closeBtn:1,            //是0为不显示叉叉 可选1,2
            shadeClose: true,    //点击其他shade区域关闭窗口
            shade: 0.5,   //透明度
            end: function () {
                location.reload();
            }
        });
    }
</script>


