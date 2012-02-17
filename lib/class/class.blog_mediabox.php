<?
class blog_mediabox extends mediabox {

	static function render ($vars) {
		/* Thing you can use
		
		### BLOG STUFF ###
		blog_id  ( int or array )
		tag  ( blog tags, int or array )
		limit
		order_by  ( defaults to 'blog_article.post_time desc' )
		most other blog parameters should work
		
		### MEDIABOX STUFF ###
		height
		width
		thumb_width (width of the thumbnail sidebar)
		interval ( waiting period between auto switching )
			
		*/
		
		$blog_vars = array();
		$properties = self::getProperties();
		foreach ($vars as $key => $var) {
			if(!in_array($key, $properties)) {
				$blog_vars[$key] = $var;
				unset($vars[$key]); 
			}
		}
		$blog_vars['mediabox'] = true;
		if (!$blog_vars['order_by'])
			$blog_vars['order_by'] = 'blog_article.post_time desc';

		$article_ids = blog_article::getList($blog_vars);

		foreach($article_ids as $article_id) {
			$blog = new blog_article($article_id);
			$rs = aql::select('blog_media { media_item_id where blog_article_id = '.$blog->blog_article_id.'}');
			$data[] = array('media_item_id'=>$rs[0]['media_item_id'],
							'tag' => $blog->blog_article_tag[0]['tag_name'],
							'title' => $blog->title,
							'subtitle' => $blog->introduction,
							'href' => '/'.$blog->blog_slug.'/'.$blog->blog_article_id.'/'.slugize($blog->title)
							);
		}
		$vars['data'] = $data;

		mediabox::render($vars);
		
	}

}