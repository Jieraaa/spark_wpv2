<?php
global $wp_query;
$search_word=$_GET['s'];
$post_status=$_GET['post_status'];
//=====获取搜索到的条目数
$query = array(
    's' => $search_word,
    'post_status' => 'publish',
    'post_type'	=> 'yada_wiki'
);
$result = new WP_Query($query);
$wiki_found = $result->found_posts;

$query = array(
    's' => $search_word,
    'post_status' => 'publish',
    'post_type'	=> 'post'
);
$result = new WP_Query($query);
$project_found = $result->found_posts;

$query = array(
    's' => $search_word,
    'post_status' => 'publish',
    'post_type'	=> 'dwqa-question'
);
$result = new WP_Query($query);
$QA_found = $result->found_posts;
//======================
$post_type= isset($_GET['post_type'])&&!empty($_GET['post_type'])?sanitize_text_field($_GET['post_type']): "dwqa-question";
$posts_per_page= isset($_GET['posts_per_page'])&&!empty($_GET['posts_per_page']) ? $_GET['posts_per_page']: 10;
$current_url= curPageURL();//设当前页面为archive页面
//翻页所需参数
$page_text = dwqa_is_front_page() ? 'page' : 'paged';
$page = get_query_var( $page_text ) ? get_query_var( $page_text ) : 1;


if($post_type=='yada_wiki'){    //根据自身情况更改
    $query_string = $query_string.'&posts_per_page=10'.'&post_type='.$post_type;
    $posts=query_posts($query_string);
}
elseif($post_type=='post'){  //根据自身情况更改
    $query_string= $query_string.'&posts_per_page=10'.'&post_type='.$post_type;
    $posts=query_posts($query_string);
}
else{
    $query_string= $query_string.'&posts_per_page=10'.'&post_type='.$post_type;
    $posts=query_posts($query_string);
}
$args = array(
    'base' => add_query_arg($page_text, '%#%', $current_url),
    'format' => '',
    'current' => $page,
    'show_all' => false,
);
$paginate = paginate_links($args);
?>

<div class="m_search_page_box">
    <form class="navbar-form " role="search" method="get" action="<?php echo home_url('/');//get_permalink() ?>" style="float: right;padding-left: 0px;padding-right: 0px">
        <div class="form-group" style="position: relative">
            <select class="form-control" id="search_select"
                    onchange="selectSearchCat(this.value);">
                <option value="qa">搜问答</option>
                <option value="wiki">搜wiki</option>
                <option value="project">搜项目</option>
            </select>
            <input type="text" id="search-content" name='s' class="form-control" placeholder="Search" value="">
            <input type="hidden" name="post_status" value="publish">
            <input type="hidden" name="post_type" id="selectPostType" value=""/>
            <button type="submit" class="btn btn-default btn-sm" id="search-btn">
                <span class="glyphicon glyphicon-search"></span>
            </button>
        </div>
    </form>
    <script>
        function selectSearchCat(value) {
            var post_type= document.getElementById("selectPostType");
            if(value=="wiki"){
                post_type.value = "yada_wiki";
            } else if(value=="project"){
                post_type.value = "post";
            } else{
                post_type.value = "";
            }
        }
    </script>
</div>

<ul id="searchTab" class="nav nav-pills">
    <?php
    $current_url = home_url(add_query_arg(array()));
    $url_array=parse_url($current_url);
    $query_parse=explode("&",$url_array['query']);
    if(array_search("post_type=yada_wiki",$query_parse)){?>
        <li class="active"><a href="<?php echo esc_url(add_query_arg( array('post_type'=>'yada_wiki','paged'=>'1') ) )?>">wiki(<?php echo $wiki_found?>)</a></li>
        <li><a href="<?php echo remove_query_arg( array('post_type','paged') ) ?>">问答(<?php echo $QA_found?>)</a></li>
        <li><a href="<?php echo esc_url(add_query_arg( array('post_type'=>'post','paged'=>'1' ) ) )?>">项目(<?php echo $project_found?>)</a></li>
    <?php }
    elseif(array_search("post_type=post",$query_parse)){?>
        <li><a href="<?php echo esc_url(add_query_arg( array('post_type'=>'yada_wiki','paged'=>'1' ) ) )?>">wiki(<?php echo $wiki_found?>)</a></li>
        <li><a href="<?php echo remove_query_arg( array('post_type','paged') ) ?>">问答(<?php echo $QA_found?>)</a></li>
        <li class="active"><a href="<?php echo esc_url(add_query_arg( array('post_type'=>'post','paged'=>'1'  ) ) )?>">项目(<?php echo $project_found?>)</a></li>
    <?php }
    else{ ?>
        <li><a href="<?php echo esc_url(add_query_arg( array('post_type'=>'yada_wiki','paged'=>'1' ) ) )?>">wiki(<?php echo $wiki_found?>)</a></li>
        <li  class="active"><a href="<?php echo remove_query_arg( array('post_type','paged') )?>">问答(<?php echo $QA_found?>)</a></li>
        <li><a href="<?php echo esc_url(add_query_arg( array('post_type'=>'post','paged'=>'1'  ) ) )?>">项目(<?php echo $project_found?>)</a></li>
    <?php } ?>
</ul>
<div class="dwqa-questions-list">
    <?php if ( have_posts() ) : ?>
        <?php while (have_posts()):the_post();?>
            <?php if ( get_post_status() == 'publish' || ( get_post_status() == 'private' && dwqa_current_user_can( 'edit_question', get_the_ID() ) ) ) : ?>
                <?php if ($post_type=='yada_wiki'){?>
                    <?php dwqa_load_template( 'Spark-wiki-search', 'archive' ) ?>
                <?php }elseif($post_type=='post'){ ?>
                    <?php dwqa_load_template( 'Spark-project-search', 'archive' ) ?>
                <?php } else{ ?>
                    <?php dwqa_load_template( 'Spark-qa-search', 'archive' ) ?>
                <?php } ?>

            <?php endif; ?>
        <?php endwhile; ?>
    <?php else : ?>
        <?php dwqa_load_template( 'Spark-content', 'none' ) ?>
    <?php endif; ?>
</div>
<div class="dwqa-questions-footer"  style="text-align: center;margin-bottom: 20px;margin-top: 10px">
    <div class="dwqa-pagination">
        <?php echo $paginate;?>
    </div>
</div>
<?php
wp_reset_query();
wp_reset_postdata();
?>
