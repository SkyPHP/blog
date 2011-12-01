<?
$sidebar = "ad";

$p->template('website', 'top');	

if ($blog_id) { ?>
	<h1><?=aql::value('blog.name',$blog_id)?></h1>
<? } else { ?>
	<h1><?=$p->seo['h1']?></h1>
	<div id="h1_blurb"><?=$p->seo['h1_blurb']?></div>
<? } 

$clause['blog_id'] = $blog_id;

$blog_articles = blog_article::getList($clause);
$grid = new array_pagination_qf($blog_articles);
if ($grid->rs) {
	foreach ( $grid->rs as $blog_article_id) {
		$r = new blog_article($blog_article_id);
		include("pages/_blog.slug_/mini.php");
	}
	?>
    <div style="display:table;margin:0 auto;">
    <?
	$grid->pages();
	?>
    </div>
	<?
} else { ?>
	There are no blog articles on this website.
<? } 

$p->template('website', 'bottom');	
?>