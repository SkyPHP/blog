<?

class blog_article extends model {

	public $_required_fields = array(
		'door_time' => 'Door Time',
		'age' => 'Age',
		'timezone' => 'Timezone',
		'ct_campaign_id' => 'Campaign'
	);

	public $_ignore = array(
		'tables' => array(
			'ct_campaign',
			'market',
			'ct_holiday'
		)
	);
	
	public function getList($a) {
		
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
		$where[]= "blog_article.status = 'A'";

		// market_id
		if ($a['market_id']) $where[] = "(blog_article.market_id = {$a['market_id']} OR blog_article.market_id = )";

        // venue_id
        if ($a['venue_id']) $where[] = "(venue.id = {$a['venue_id']} or bar.id = {$a['venue_id']})";

        // search
        if ($a['search']) {
        	$search = trim(addslashes($a['search']));
        	$search = " ilike '%{$search}%' ";
        	$where[] = "(blog_article.name {$search} or venue.name {$search} or bar.name {$search} or ct_contract.name {$search})";
        }

        // limit 
        if ($a['limit']) $limit = 'LIMIT '.$a['limit'];

        // offset
        if ($a['offset']) $offset = 'OFFSET '.$a['offset'];

        //order_by
        $order_by = 'ORDER BY ';

        $where = ($where) ? implode(' and ', $where) : 'true';

		$clause_array = array(	'limit' => $a['limit'],
								'where' => $where
								);
		$blog_articles = model::getByClause($clause_array, 'blog_article');
       
		return $blog_articles;
	
	}
	
}