<?
$sidebar = "ad";

$p->template('website', 'top');	





$clause = array(
			'blog_id' => 1
			);
$blog_articles = blog_article::getList($clause);

krumo($blog_articles);

foreach($blog_articles as $blog_article_id) {
	$ba = new blog_article($blog_article_id);
	krumo($ba);
	?>
    <div style="height:14px;"><?=$ba['title']?></div>
    <div style="height:14px;"><?=$ba['status']?></div>
    <div style="height:14px;"><?=$ba['market_id']?></div>
    <div style="height:14px;"><?=$ba['post_time']?></div>

    <?
	include("pages/_blog.slug_/mini.php");
}


$p->template('website', 'bottom');	
?>