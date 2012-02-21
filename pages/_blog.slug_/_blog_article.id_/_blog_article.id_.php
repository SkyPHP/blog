<?
$sidebar = 'ad';

$r= new blog_article($blog_article_id);

if ($p->queryfolders[0] != slugize($r->title)){  
	if(IDE == $r->blog_slug)
		redirect('/'.$r->blog_slug);
	else
		redirect('/blog');
}



$p->js[]='http://maps.google.com/maps/api/js?sensor=false&language=en';
    

$p->breadcrumb = array( 'Home' => '/',
						$r->blog_name => '/'.$r->blog_slug,
						$r->title => NULL);

$p->template('website', 'top');	
include("article.php");

$p->template('website', 'bottom');	


?>