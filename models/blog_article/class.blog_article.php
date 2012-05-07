<?

class blog_article extends Model {

	public $_required_fields = array(
		
	);

	public $_ignore = array(
		'tables' => array(
			'market', 'blog', 'person', 'blog_category'
		)
	);
	
	public static function getList($a) {		

		//. market_id
		//. where
		//. status
		//. blog_category_id
		//. limit
		//. order_by
		//. offset
		//. search
		//. group_by
		
		// where
		if ($a['where']) {
			if (is_array($a['where'])) {
				$where = $a['where'];
			} else {
				$where = array($a['where']);
			}
		} else {
			$where = array();
		}
		$where[] = 'blog_article.id IS NOT NULL';
		$where[] = 'blog_article.post_time < now()';

		// market_id
		if ($a['market_id']) $where[] = "(blog_article.market_id = {$a['market_id']} OR blog_article.market_id = 0 OR blog_article.market_id IS NULL)";

		// status
		if ($a['status']) {
			$where[] = "blog_article.status = '{$a['status']}'";
		} else {
			$where[] = "blog_article.status = 'A'";
		}
	
		//ct_category_instance_id
        if ($a['blog_category_id']) {
        	$where[] = 'blog_category.id = '.$a['blog_category'];
        }
		
		// mediabox
		if ($a['mediabox']) {
			$where[] = "blog_media.type = 'mebox'";
		}
        
		// venue_id
        if ($a['venue_id']) $where[] = "blog_article.venue_id = {$a['venue_id']}";

        // search
        if ($a['search']) {
        	$search = trim(addslashes($a['search']));
        	$search = " ilike '%{$search}%' ";
        	$where[] = "(blog_article.title {$search} or blog_article.content {$search})";
        }
		// person_id
		if ($a['person_id']) {
			$where[] = "blog_article.author__person_id = ".$a['person_id'];
		}
        // blog_id
		if ($a['blog_id']) {
			if (is_array($a['blog_id'])) {
				$where[] = "blog_article.blog_id in (".implode(',',$a['blog_id']).")";	
			} else {
				$where[] = "blog_article.blog_id = ".$a['blog_id'];
			}
		}
		
		// limit 
        if ($a['limit']) 
			$limit = 'LIMIT '.$a['limit'];
		//else
		//	$limit = 'LIMIT 10';

        // offset
        if ($a['offset']) $offset = 'OFFSET '.$a['offset'];

        //order_by
        $order_by = 'ORDER BY ';
        if ($a['order_by']) $order_by .= $a['order_by'] . ', ';
        $order_by .= 'blog_article.post_time desc';

        //group_by
        if ($a['group_by']) {
        	$group_by = 'GROUP BY '.$a['group_by'];
        }

        $from = 'blog_article';
        if ($a['tag']) {
        	$from = 'blog_article_tag';
        	$first_join = 'LEFT JOIN blog_article on blog_article.id = blog_article_tag.blog_article_id and blog_article.active = 1';
			if( is_array($a['tag']) ) {
				unset($tag_temp);
				foreach($a['tag'] as $blog_tag) {
					$tag_temp .= "blog_article_tag.name ilike '".$blog_tag."' or ";
				}
				$where[] = '('.$tag_temp.' false)';
			} else {
				$where[] = "blog_article_tag.name ilike '".$a['tag']."'";
			}
		} else {
        	$tag_join = 'LEFT JOIN blog_article_tag on blog_article.id = blog_article_tag.blog_article_id and blog_article_tag.active = 1';
        }

        $where = ($where) ? implode(' and ', $where) : 'true';

        $sql = "SELECT blog_article_id FROM (
	        		SELECT DISTINCT on (q.blog_article_id) blog_article_id, row FROM (
	        			SELECT
	        				blog_article.id as blog_article_id,
	        				row_number() OVER ({$order_by} ) as row
	        			FROM {$from}
	        			{$first_join}
	        			LEFT JOIN blog on blog.id = blog_article.blog_id and blog.active = 1
	        			LEFT JOIN blog_category on blog_category.id = blog_article.blog_category_id and blog_category.active = 1
	        			LEFT JOIN blog_media on blog_media.blog_article_id = blog_article.id and blog_media.active = 1

	        			{$tag_join}
	        			WHERE blog_article.active = 1 AND {$where}
	        			{$group_by}
	        			{$order_by}
	        			
	        		) as q
	        	) as fin ORDER BY row
				{$offset}
	        	{$limit}";
       	elapsed('before blog_article::getList()');
        $r = sql($sql);
        elapsed('after blog_article::getList()');
		$ids = array();
		while (!$r->EOF) {
			$ids[] = $r->Fields('blog_article_id');
			$r->moveNext();
		}
		return $ids;
	}
	
}