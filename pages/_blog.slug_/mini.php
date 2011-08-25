<?

global $market_slug;
global $cur_page;

if($_GET['debug']){
   echo "<!-- ";
   var_dump($r);
   echo " -->";
}

$blog_article_id = $r['blog_article_id'];

if(!$r['blog_article_ide']){$r['blog_article_ide']=$blog_article_ide;}
if(!$r['market_slug']){$r['market_slug']=$market_slug;}
if(!$r['market_slug']){$r['market_slug']=$_SESSION['market_slug'];} // fix for an error where $market_slug is not initialized

//if ($GLOBALS['blog_has_markets']) {
//    $article_uri = '/'.$r['market_slug'].'/' . $r['blog_slug'] . '/'.$blog_article_id."/".slugize($r['title']).'/';
//} else {
    $article_uri =  '/'.$r['blog_slug'] . '/'.$blog_article_id.'/'.slugize($r['title']).'/';
//}
$article_tinyurl = tinyurl( 'http://' . $_SERVER['HTTP_HOST'] . $article_uri );

// remove html garbage, images and empty paragraphs
$content = $r['content'];
$content = str_replace('&nbsp;',' ',$content);
$pattern_caption = '#<div[^>]*class=[^>]*article_image[^>]*>[^<]*(<img[^>]*>)[^<]*(<div[^>]*>[^<]*</div\s*>)*[^<]*(<div[^>]*>[^<]*</div\s*>)*[^<]*</div\s*>#';
$replacement = '';
$content = preg_replace($pattern_caption, $replacement, $content);

$content = strip_tags($content,'<a><p><span><i><br><ul><ol><li><hr>');
$pattern = '#<p.*>\s*</p>#m';
$content = preg_replace($pattern, $replacement, $content, -1, $count);

$cutoff = strpos($content,'<hr');
$content = str_replace(array('<hr>','<hr/>','<hr />'),'',$content);

$full = strlen($content);
debug('full: ' . $full);
$mini_length = 600;
$mini_overflow = 200;

// default image size
$image_width = 240;
$image_height = 360;

$sm_image_width = 150;
$sm_image_height = 400;


if ( $cutoff ) {

    $content = substr($content,0,$cutoff);
    $read_more = true;

} else if ( $full > 1000 ) {

    // larger article

    $cutoff = strpos($content, '<p', $mini_length);

    if ( $full - $cutoff  > $mini_overflow ) {
        $content = substr($content,0,$cutoff);
        $read_more = true;
    } else {
        $read_more = false;
    }


} else if ( $full < 300 ) {

    // show smaller image for tiny post
    $image_width = $sm_image_width;
    $image_height = $sm_image_height;

}


?>


<div class="blog-mini-post">
    <div class="blog-mini-top">

<?
    //------ IMAGE ------
    $img = vf::getItem($r['media_item_id'],array('width'=>$image_width,'height'=>$image_height));
    if ($img) {
        if ( $img->width < $image_width ) 
			$img = vf::getItem($r['media_item_id'],array('width'=>$sm_image_width,'height'=>$sm_image_height));
	
?>

        <div class="blog-post-image">
<?
        if($r['blog_article_tag'][0]['tag_name'] || $r['name'])	{
			if (strtolower($r['blog_article_tag'][0]['tag_name'])) {
?>
                <a href="<?=$article_uri ?>" class="blog-category">
                    <?=strtoupper($r['blog_article_tag'][0]['tag_name'])?>
                </a>
<?
			}else{
	?>         <a href='<?=$article_uri ?>' class="blog-category">
				  <?=strtoupper($r['name'])?>
			   </a>  
<?
			}
        }//if
?>
            <a href="<?=$article_uri?>"><?=$img->html?></a>
        </div>
<?
    } else if (strtolower($r['blog_article_tag'][0]['tag_name'])) {
?>
        <a href="<?=$article_uri ?>" class="blog-category blog-category-alone">
            <?=strtoupper($r['blog_article_tag'][0]['tag_name'])?>
        </a>
<?
    }//if

?>
    <div class="blog-post-title">
        <a href="<?=$article_uri?>"><?=$r['title']?></a>
    </div>

<? 
    if ( $r['fname'] || $r['lname'] ) {
?>
        <div class="blog-post-author">by
           <? if (!$hide_blog_author_url) { ?>
                <?=$cur_page!="blog/author"?($r['username']?"<a href='/".$r['username']."' >":"<b>"):"<b>"?><?=ucwords(strtolower($r['fname']." ".$r['lname']))?><?=$cur_page!="blog/author"?($r['username']?"</a>":"</b>"):"</b>"?>
           <? } else { ?>
			<b><?=ucwords(strtolower($r['fname']." ".$r['lname']))?></b>
		  <? } ?>
            &nbsp; <span class="blog-post-time"><?=date('D, F jS, Y',strtotime($r['post_time']))?> at <?=date('g:ia',strtotime($r['post_time']))?></span>
        </div>
<?
    }//if
    
    echo $content;
?>

        <div class="blog-mini-footer">
<?
            if ( $read_more ) {
?>
                <a href="<?=$article_uri?>" class="blog-read-more">Read More &raquo;</a>
<?
            }//if
?>

               

                <div class="blog-mini-share">
                    <!-- AddThis Button BEGIN -->
                    <div class="addthis_toolbox addthis_default_style">
                    <a href="http://addthis.com/bookmark.php?v=250&amp;username=xa-4c057f8453f8589f"
                       class="addthis_button_compact"
                       addthis:title="<?=$r['title']?>"
                       addthis:url="<?=$article_tinyurl?>">Share</a>
                    <span class="addthis_separator">|</span>
                    <a class="addthis_button_facebook"
                       addthis:title="<?=$r['title']?>"
                       addthis:url="<?=$article_tinyurl?>"></a>
                    <a class="addthis_button_myspace"
                       addthis:title="<?=$r['title']?>"
                       addthis:url="<?=$article_tinyurl?>"></a>
                    <a class="addthis_button_google"
                       addthis:title="<?=$r['title']?>"
                       addthis:url="<?=$article_tinyurl?>"></a>
                    <a class="addthis_button_twitter"
                       addthis:title="<?=$r['title']?>"
                       addthis:url="<?=$article_tinyurl?>"></a>
                    <span class="addthis_separator">|</span>
                    <a class="dsq-comment-count" href="/<?=$r['market_slug']?"{$r['market_slug']}/":($market_slug?"$market_slug/":'')?><?=$r['blog_slug']?>/<?=slugize($r['title'])?>/<?=$r['blog_article_ide']?>#disqus_thread">Comments</a>
                    </div>
                    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4c057f8453f8589f"></script>
                    <!-- AddThis Button END -->
                </div>
<?
/*
?>
                <div class="blog-mini-comments">
                    <a href="<?=$article_uri?>#comments" class="count">5</a>
                    <a href="<?=$article_uri?>#comments">Comments</a>
                </div>
<?
*/
?>

        </div>
    </div>
</div>
