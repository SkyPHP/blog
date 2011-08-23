<?

$current_tag = strtolower($p->queryfolders[0]);
$current_tag = preg_replace('#[^a-zA-Z0-9_ ]#','%',$current_tag);


$rs = aql::select("blog_article_tag{ where blog_article_tag.name ilike '".$current_tag."'}");
echo '<div style="background-color:#fff;">';
//$sql = aql::sql("blog_article_tag{ where blog_article_tag.name ilike '".$current_tag."'}");
//echo $sql['sql'];
//echo sizeof($rs);
echo '</div>';

$clause['tag'] = $current_tag;



$p->css[] = '/pages/_blog.slug_/_blog.slug_.css';

include('pages/_blog.slug_/_blog.slug_.php');