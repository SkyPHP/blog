<?

class blog_article extends model {

	public $_required_fields = array(
		
	);

	public $_ignore = array(
		'tables' => array(
		)
	);
	
	public function getList($a) {		

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

		// market_id
		if ($a['market_id']) $where[] = "blog_article.market_id = {$a['market_id']}";

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

        // venue_id
        if ($a['venue_id']) $where[] = "venue.id = {$a['venue_id']}";

        // search
        if ($a['search']) {
        	$search = trim(addslashes($a['search']));
        	$search = " ilike '%{$search}%' ";
        	$where[] = "(blog_article.title {$search} or blog_article.content {$search})";
        }

        // limit 
        if ($a['limit']) $limit = 'LIMIT '.$a['limit'];

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

        $where = ($where) ? implode(' and ', $where) : 'true';

        $sql = "SELECT blog_article_id FROM (
	        		SELECT DISTINCT on (q.blog_article_id) blog_article_id, row FROM (
	        			SELECT
	        				blog_article.id as blog_article_id,
	        				row_number() OVER ({$order_by} ) as row
	        			FROM blog_article
	        			LEFT JOIN blog_article_tag on blog_article_tag.blog_article_id = blog_article.id and ct_event.active = 1
	        			LEFT JOIN blog on blog.id = blog_article.blog_id and blog.active = 1
	        			LEFT JOIN blog_category on blog_category.id = blog_article.blog_category_id and blog_category.active = 1
	        			
	        			WHERE blog_article.active = 1 AND {$where}
	        			{$group_by}
	        			{$order_by}
	        			{$offset}
	        			{$limit}
	        		) as q
	        	) as fin ORDER BY row";
        $r = sql($sql);
		$ids = array();
		while (!$r->EOF) {
			$ids[] = $r->Fields('blog_article_id');
			$r->moveNext();
		}
		return $ids;
	}
	
}