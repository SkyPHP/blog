<?
$sidebar = "ad";

$p->template('website', 'top');	
?>
<h1><?=aql::value('blog.name',$blog_id)?></h1>
<?

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
}

$p->template('website', 'bottom');	
?>