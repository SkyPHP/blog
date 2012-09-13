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
		
		$parsed = self::separateProperties($vars, array(
			'mediabox' => true
		));

		$blog_vars = $parsed['other'];
		$vars = $parsed['mediabox'];

		$blog_vars['order_by'] = ($blog_vars['order_by']) ?: 'blog_article.post_time desc';
		$blog_vars['limit'] = ($blog_vars['limit']) ?: 5;
		print_a($blog_vars);

		$article_ids = blog_article::getList($blog_vars);
		$media = self::getMediaIDs($article_ids);

		foreach ($article_ids as $id) {
			$o = new blog_article($id);
			$data[] = array(
				'media_item_id' => $media[$o->getID()],
				'tag' => $o->blog_article_tag[0]['tag_name'],
				'title' => $o->title,
				'subtitle' => $o->introduction,
				'href' => sprintf(
					'/%s/%s/%s', 
					$o->blog_slug, 
					$o->blog_article_id, 
					slugize($o->title)
				)
			);
		}

		$vars['data'] = $data;

		return mediabox::render($vars);
	}

	// return an associative array of media_item_ids by blog_article_id
	public static function getMediaIDs($ids = array()) {
		
		if (!$ids) return array();

		if (!is_array($ids) || is_assoc($ids)) {
			throw new Exception('blog_mediabox::getMediaIDs() expects an array parameter');
		}

		$ids_in = sprintf('blog_media.blog_article_id in (%s)', implode(',', $ids));
		$aql = "blog_media { media_item_id, blog_article_id } ";
		$rs = aql::select($aql, array(
			'where' => $ids_in
		));

		$re = array();
		foreach ($rs as $r) {
			$re[$r['blog_article_id']] = $r['media_item_id'];
		}

		return $re;

	}

}