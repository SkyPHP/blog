<?

$aql = "blog_article_video{
			youtube_url as video_url,
			type,
			title
			where blog_article_id = {$r['blog_article_id']}
			order by type desc
		}";
$rs = aql::select($aql);
if($rs){
?>
	<div class="blog-video">
    	<center> 
<?
	$width = 480;
	foreach($rs as $media){	
		$parsed_url = parse_url($media['video_url']);
		if($parsed_url['host'] == 'www.youtube.com') {
			include('embed-youtube.php');
		}
		if($parsed_url['host'] == 'vimeo.com') {
			include('embed-vimeo.php');
		}
	}
?>
		</center>
	</div>
<?
}
?>