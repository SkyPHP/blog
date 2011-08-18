<?
$p->template('website', 'top');	







$where = array(
    "blog_article.blog_id = 1"
);
//blog::listing( $where, 10, 0, NULL, NULL);

$clause = array(
			'limit' => 10
			);
$blog_articles = blog_article::getList($clause);
krumo($blog_articles);


$p->template('website', 'bottom');	
?>