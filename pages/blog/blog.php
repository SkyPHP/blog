<?
$json = json_decode($website->blog_json);
if($json->blog_id)
	$blog_where[] = "blog.id IN (".implode(', ',$json->blog_id).")";
if($json->blog_tag)	
	$blog_where[] = "(blog_article_tag.name ILIKE '".implode("' OR blog_article_tag.name ILIKE '",$json->blog_tag)."')";

// IF NO $blog_where, set $blog_where to flase so no blogs show up.
if(!$blog_where)
	$blog_where[] = 'false';

$clause['where'] = "( ".implode(' AND ',$blog_where)." )" ;

$this->css[] = '/pages/_blog.slug_/_blog.slug_.css';

elapsed('before incl _blog.slug_.php');
include('pages/_blog.slug_/_blog.slug_.php');