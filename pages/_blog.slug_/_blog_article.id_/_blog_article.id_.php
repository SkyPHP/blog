<?
$sidebar = 'ad';

$r= new blog_article($blog_article_id);

$p->js[]='http://maps.google.com/maps/api/js?sensor=false&language=en';
    

$p->breadcrumb = array( 'Home' => '/',
						$r->blog_name => '/'.$r->blog_slug,
						$r->title => NULL);

$p->template('website', 'top');	
krumo($r);
include("article.php");

$p->template('website', 'bottom');	


?>