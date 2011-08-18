<?

#
#
#

class blog {

	public $blog_id;
	public $blog_name;

	function __construct( $blog_slug ) {
		$aql = "blog {
					id,
					name as blog_name,
                    has_markets
					where blog.slug = '$blog_slug'
				}";
		$rs = aql::select($aql);
		$this->blog_id = $rs[0]['blog_id'];
		$this->blog_name = $rs[0]['blog_name'];
        $this->has_markets = $rs[0]['has_markets'];
	}//constructor

	function html() {
		global $website_id, $db;
		
		if ( $this->where ) $where = ' and ' . $this->where;
		
		$limit = 10;
		if ( is_numeric($this->limit) ) $limit = $this->limit;
		
		$aql = "blog_article {
					blog_id,
					title,
					introduction,
					content,
					author__person_id,
					approved__person_id,
					post_time,
					blog_category_id,
					status,
					comment_count,
					market_id,
					featured,
					media_item_id,
					tweet_sent
					
					where blog_article.blog_id = " . $this->blog_id . "
					and blog_article.status = 'A'
					$where
					order by post_time desc
					limit $limit
				}
				blog {
					slug as blog_slug
				}
				blog_website {
					where blog_website.website_id = $website_id
				}
				";
		$rs = aql::select($aql);
		if ( is_array($rs) ):
?>
			<script type="text/javascript">
				add_style('/modules/blog/blog.css');
				add_javascript('/modules/blog/blog.js');
			</script>
<?
			foreach ( $rs as $row ):
				include('components/blog_article/id/article.php');
			endforeach;
		endif;
	}

    
################################################################################

    
	function listing($where=NULL,$limit=10,$page_number=0,$articles=NULL,$disqus=true) {
		global $hide_blog_author_url;
        //$articles param allows you to pass an array of articles directly
        if(is_array($articles)){
           $i = 0;
           foreach($articles as $r){
              include('components/blog_article/id/mini.php');
              if($limit){
                 if(++$i>=$limit){
                    break;
                 }
              }
           }
        } else {
           if ( !is_array($where) ) $where = array( $where );

           $where[] = "blog_article.status = 'A'";
           $where[] = "blog_article.post_time <= now()";

           $clause = array(
               'blog_article' => array(
                   'where' => $where,
                   'order by' => 'post_time desc',
                   'limit' => $limit+1,
                   'offset' => $limit*($page_number)
               )
           );
           $rs = aql::select(aql::get_aql('blog_article'),$clause);

           if(count($rs)<=$limit){global $last_page; $last_page=true;}

           if ( is_array($rs) ):
              foreach ( $rs as $r ):
                 include('components/blog_article/id/mini.php');
              endforeach;
           endif;

           
       }

           #this is to work around a bad interaction between disqus comment_count and jScrollPane
           #if disqus comment_count is an ancestor of jScrollPane div, the browser executes disqus code more than once, the second time resulting in the browser loading to a disqus ajax request
           #to work around this, set $disqus to false and manually include necesary disqus files so that they are printed to the page outside of the jScrollPane div
           if($disqus){
              include_once('components/disqus/comment_count/comment_count.php');
           }
	}


################################################################################

	
	function dashboard() {
		include('pages/admin/blog/dashboard/dashboard.php');
	}

	function marquee($blog_article_array, $settings=0){
           $class = $settings['class']?$settings['class']:'marquee';
           $file = $settings['file']?$settings['file']:'pages/blog/marquee/marquee.php';
           $padding = $settings['padding']?$settings['padding']:2;

	   $duration = $settings['duration']?$settings['duration']:2000;
	   $transition = $settings['transition']?$settings['transition']:"fade";
	   $height = $settings['height']?$settings['height']:320;
	   $width = $settings['width']?$settings['width']:640;	   
	   $height_small = $settings['height_small']?$settings['height_small']:50;
	   $width_small = $settings['width_small']?$settings['width_small']:85;
           $width_bground = $width-$width_small-(2*$padding);

           $crop_gravity = $settings['crop_gravity']?$settings['crop_gravity']:'center';
           $crop_gravity_small = $settings['crop_gravity_small']?$settings['crop_gravity_small']:'center';

           $css = $settings['css']?$settings['css']:null;

           $speed = $settings['speed']?$settings['speed']:'null';
           $easing = $settings['easing']?$settings['easing']:'null';
 

           $get_item_settings = array('crop_gravity'=>$crop_gravity,'upsize'=>true);
           $get_item_settings_small = array('crop_gravity'=>$crop_gravity_small,'upsize'=>true);
          
	   $index=0;

       if (!is_array($blog_article_array)) return false;

       ?><script type='text/javascript'>
	      var mediaboxvars = new Array();<?
       foreach($blog_article_array as $blog_article){
                 $img = media::get_item($blog_article['media_item_id'],$width_bground,$height,true,NULL,$get_item_settings);
                 $imgsm = media::get_item($blog_article['thumb__media_item_id'],$width_small,$height_small,true,NULL,$get_item_settings_small);
                 $article_title = str_replace( '"', '&quot;', $blog_article['title'] );
                 ?>
	         mediaboxvars[<? echo $index++ ?>]={
                    "title": "<?= htmlspecialchars($blog_article['title'],ENT_QUOTES) ?>",
                    "subtitle": "<?= htmlspecialchars($blog_article['introduction'],ENT_QUOTES) ?>",
                    "toolTip": "<?= htmlspecialchars($blog_article['title'],ENT_QUOTES) ?>",
                    "link": "<?=$blog_article['link']?>",
                    "img": "<?=$img['src'] ?>",
                    "imgsm": "<?=$imgsm['src'] ?>",
                    "blogname": "<?= htmlspecialchars($blog_article['name'],ENT_QUOTES) ?>",
                    "dimension": {'height':"<?=$img['height']?>",'width':"<?=$img['width']?>"},
                    "dimension_thumb": {'height':"<?=$imgsm['height']?>",'width':"<?=$imgsm['width']?>"}
	         };
                 <?
	      }
	   ?>
	      mediaboxvars['duration']=<?=$duration ?>;
	      mediaboxvars['transition']="<?=$transition ?>"; 
              mediaboxvars['speed']="<?=$speed ?>";

              <? if($easing!='null'){ ?>
                 mediaboxvars['easing']="<?=$easing ?>";
              <? } ?>

	      add_css("/pages/blog/marquee/marquee.css");
              add_js("/lib/js/jquery/jquery.easing.1.1.1.js");
            //  add_js("/lib/js/jquery/jquery.easing.compatibility.js");
	      add_js("/lib/js/jquery/jquery.cycle.all.js");
	      add_js("/pages/blog/marquee/marquee.js");
	      </script>

	   <?

	   if($settings['height'] || $settings['width']){
	      ?>
	         <style type='text/css'>
	            #marquee{width:<?=$width ?>px !important;height: <?=$height ?>px !important;}
	            #marquee_text{height:<?=($height*.288) ?>px;}
	            #marquee_images{max-width:<?=$width_small ?>px; padding-right:<?=$padding ?>px; padding-top:<?=$padding ?>px; margin-bottom:<?=$padding ?>px;}
                    .marquee_image_wrap{width:<?=$width_small ?>px; height:<?=$height_small ?>px;}
	            <? /*#marquee_blogname{top:<?=(($height*.288)-17) ?>px;}*/ ?>
	            #marquee_text_content{width:<?=($width-(13+5+$width_small+5)) ?>px;}
	         </style>
	      <?
	   }

           if(is_array($css)){
           ?> <style type='text/css'> <?
           foreach($css as $key => $style){
              ?>
                    <?=$key ?> { <?=$style ?>; }
              <?
           }//foreach
           ?> </style> <?
           }//if

           include($file);
	}

    function get_blogs() {
        global $website_id;
        $aql = "blog_website {
                    where website_id = $website_id
                    and blog_website.status = 'A'
                    order by blog_website.iorder asc
                }
                blog {
                    has_markets,
                    category as blog_category,
                    slug as blog_slug,
                    name as blog_name
                }";
        $rs = aql::select($aql);
        return $rs;
    }

    function incriment_pageviews($blog_article_id){
       if(is_numeric($dump=decrypt($blog_article_id,'blog_article'))){
          $blog_article_ide = $blog_article_id;
          $blog_article_id = $dump;
       }

       $aql =
       "blog_article{
          pageviews
          where id=$blog_article_id  
       }";

       $rs = aql::select($aql);

       if(is_array($rs)){
          $pageviews = $rs[0]['pageviews'];
          return(aql::update('blog_article',array('pageviews'=>++$pageviews),$blog_article_id)?$pageviews:'Error incrimenting page count');          
       }else{
          return('invalid blog_id');
       }
    }

    function get_pageviews($table, $obj_id=NULL){
       $error_str="\$table=\'$table' \$obj_id=\'$obj_id\' <br><br>\n\n";
       if(!$obj_id){
          $order_by = $obj_id['order by']?$obj_id['order by']:'order by sum desc ';
          $limit = $obj_id['limit']?$obj_id['limit']:'limit 20 ';
          $offset = $obj_id['offset']?$obj_id['offset']:'offset 0 ';
          $where = 'where true ';
       }else{
          if(is_array($obj_id)){
             $order_by = $obj_id['order by']?$obj_id['order by']:'order by sum desc ';
             $limit = $obj_id['limit']?$obj_id['limit']:'limit 20 ';
             $offset = $obj_id['offset']?$obj_id['offset']:'offset 0 ';
             $where = $obj_id['where']?$obj_id['where']:'where true ';
          }elseif(is_numeric($dump=decrypt($obj_id,$table))){
             $obj_ide = $obj_id;
             $obj_id = $dump;
          }elseif(!is_numeric($obj_id)){
             //string given, may be meaningful
             switch($table){
                case('blog_article_tag'):
                   //tag value
                   $where = "where a.name=lower('$obj_id') ";
                   break;
                case('blog_author'):
                case('person'):
                   //author last name
                   $where = "where lower(lname)=lower('$obj_id') ";
                   break;
                case('blog'):
                   //blog name
                   $where = "where lower(name)=lower('$obj_id') ";
                   break;
                default:
                   return('improper table specified: '.$error_str);
             }
          }else{
          }
       }
 
       if(!$where){
           switch($table){
                case('blog_article_tag'):
                   $dump = aql::select("blog_author_tag{name where id=$obj_id}");
                   if(!is_array($dump)){return('No tag with id: '.$error_str);}
                   $name = $dump[0]['name'];
                   $where = "where a.name=lower('$name') ";
                   break;
                case('blog_author'):
                   $dump = aql::select("blog_author{person_id as name where id=$obj_id}");
                   if(!is_array($dump)){return('No author with id: '.$error_str);}
                   $obj_id = $dump[0]['name'];
                case('person'):
                   $where = "where a.person_id=$obj_id ";
                   break;
                case('blog'):
                   $where = "where a.blog_id=$obj_id ";
                   break;
                case('blog_article'):
                   $where = "where id=$obj_id";
                   break;
                default:
                   return('improper table specified: '.$error_str);
             }
       }

       if($where === 'where true '){$where = '';}

       switch($table){
          case('blog_article_tag'):
             $sql = "select distinct lower(bat.name) as name, a.sum as sum, a.count, case when a.count!=0 then (a.sum/cast(a.count as float)) else 0 end as ratio from blog_article_tag as bat inner join (select sum(pageviews) as sum, lower(blog_article_tag.name) as name, count(distinct blog_article_id) as count from blog_article inner join blog_article_tag on blog_article_tag.blog_article_id=blog_article.id group by lower(blog_article_tag.name)) as a on lower(bat.name)=a.name $where $order_by $limit $offset";
             break;
          case('blog_author'):
          case('person'):
             $sql = "select a.person_id as person_id, fname,lname, fname||' '||lname as name,  a.sum as sum, a.count as count, case when a.count!=0 then (a.sum/cast(a.count as float)) else 0 end as ratio from person as p inner join (select sum(blog_article.pageviews) as sum,count(distinct blog_article.id) as count,person.id as person_id from blog_article inner join person on blog_article.author__person_id=person.id group by person.id) as a on a.person_id=p.id $where $order_by $limit $offset";
             break;
          case('blog'):
             $sql = "select a.blog_id as blog_id, name, a.sum as sum, a.count as count, case when a.count!=0 then (a.sum/cast(a.count as float)) else 0 end as ratio from blog as b inner join (select blog.id as blog_id, sum(blog_article.pageviews) as sum, count(distinct blog_article.id) as count from blog inner join blog_article on blog.id=blog_article.blog_id group by blog.id) as a on b.id=a.blog_id $where $order_by $limit $offset";
             break;
          case('blog_article'):
             $sql = "select id as blog_article_id, title as name, title as title, pageviews as sum, NULL as count, NULL as ratio from blog_article $where $order_by $limit $offset";
             break;
          default:
             return('improper table specified: '.$error_str);
       }

       global $db;
       $rs = $db->Execute($sql) or die($db->ErrorMsg());

       $results = array();

       while(!$rs->EOF){
          $results[]=$rs->fields;
          $rs->MoveNext();
       }

       return(count($results)==1?$results[0]:$results); 
    }

	function testest(){
	   $aql = "
	      blog_article{
	         title, media_item_id, introduction
	         where blog_article.status = 'A' and
	         blog_article.featured = 1
	         order by post_time desc
	         limit 5
	      }
	      blog on blog.id=blog_article.blog_id{
	         slug
	      }";
	   $rs = aql::select($aql);

	   foreach($rs as $i){
	   }

	   $settingss = array("transition" => "scrollVert", "duration" => 2500 , "height" => 300, "width" => 500 , "height_small" => 50, "width_small"=> 50 );

           blog::marquee($rs, $settingss);
	}

}//cache

?>
