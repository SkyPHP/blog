<?
		$type = $media['type'];
		$height = $type == 'mp3'?25:385;
		$youtube_url = $media['video_url'];
		$youtube_url = parse_url($youtube_url);
		$query = $youtube_url['query']?$youtube_url['query']:$youtube_url['fragment'];
		parse_str($query, $youtube_params);
		$youtube_id = $youtube_params['v']?$youtube_params['v']:$youtube_params['!v'];

	if($media['title']){
?>
		<h3><?=$media['title']?></h3>
<?	
	}
?>
		<object width="<?=$width ?>" height="<?=$height ?>">
			<param name="movie" value="http://www.youtube.com/v/<?=$youtube_id?>&hl=en&fs=1&rel=0&color1=0×4fe6ef&color2=0xd0e0e6"></param>
			<param name="allowFullScreen" value="true"></param>
			<param name="allowscriptaccess" value="always"></param>
			<embed src="http://www.youtube.com/v/<?=$youtube_id?>&hl=en&fs=1&rel=0&color1=0×4fe6ef&color2=0xd0e0e6" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="<?=$width ?>" height="<?=$height ?>"></embed>
		</object>