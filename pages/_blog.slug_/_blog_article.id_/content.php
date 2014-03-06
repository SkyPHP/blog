<?

    $content = $r->content;
    // add captions to photos
    $pattern = '/<img[^>]+\>/i';
    preg_match($pattern,$content,$matches);
    if ( is_array($matches) )
    foreach ($matches as $img) {
        $pattern = '#/media/([^"]+)/#';
        preg_match($pattern,$img,$matches2);
        $media_instance_ide = $matches2[1];
        if ( !is_numeric( decrypt($media_instance_ide,'media_instance') ) ) continue;
        // NEW CAPTION CODE WILL NEED TO BE ADDED HERE
        /*$aql = "media_item {
                    caption,
                    credits
                }
                media_instance {
                    where media_instance.ide = $media_instance_ide
                }";
        $rs = aql::select($aql);
        $rs = $rs[0];*/


        if ( $rs['caption'] || $rs['credits'] ) {
            $replace = '<div class="article_image">'.str_replace('article_image','',$img);
            if ($rs['caption']) $replace .= '<div class="article_image_caption">'.$rs['caption'].'</div>';
            if ($rs['credits']) $replace .= '<div class="article_image_credits">'.$rs['credits'].'</div>';
            $replace .= '</div>';
            $content = str_replace( $img, $replace, $content );
        }
    }
	// add joonbug url to photo src
	$pattern = '/<img(.+)src="\/(.+)"/';
	$replacement = '<img${1}src="http://joonbug.com/${2}"';
	$content = preg_replace($pattern, $replacement, $content);
	//if image is 600px wide, remove height, change width to 700 for full width action
	$pattern = '/<img(.+)width="(?P<width>\d+)"(.+)(?P<height>height="(\d+)")/';
	preg_match($pattern, $content, $matches);
	if ($matches['width'] == '600') {
		$replacement = '<img${1}width="700"';
		$content = preg_replace($pattern, $replacement, $content);
	}