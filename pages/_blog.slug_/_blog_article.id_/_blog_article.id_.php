<?php
$this->sidebar = 'ad';
$this->tab = 'blog';
$r= new blog_article($blog_article_id);

if ($this->queryfolders[0] != slugize($r->title)){  
	if (IDE == $r->blog_slug)
		redirect('/'.$r->blog_slug);
	else {
		redirect('/blog');
	}
}

$this->js[]='http://maps.google.com/maps/api/js?sensor=false&language=en';

$this->breadcrumb = array( 'Home' => '/',
						$r->blog_name => '/'.$r->blog_slug,
						$r->title => NULL);

$this->template('website', 'top');	
include("article.php");

$this->template('website', 'bottom');