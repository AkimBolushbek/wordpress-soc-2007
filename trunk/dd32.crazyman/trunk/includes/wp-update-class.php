<?php
class WP_Update{
	function WP_Update(){
		require_once( ABSPATH . 'wp-includes/class-snoopy.php' );
	}
	function search($item='themes',$tags=array(),$output='array',$page=1){
		if('themes' == $item){
			if(0 == count($tags)) return false; //Must specify search terms.
			if('array' == $output) return $this->searchThemes($tags,$page);
			if('html' == $output){ //Format it for HTML output.
				$items = $this->searchThemes($tags,$page);
				$ret = '';
				foreach($items as $theme){
					$ret .="&nbsp;<div class='themeinfo'><span>
							<a href='{$theme['url']}' title='{$theme['name']}' target='_blank'>{$theme['name']}<br />
							<img src='{$theme['snapshot']['thumb']}' alt='{$theme['name']} - Downloaded {$theme['downloadcount']} times' 
							title='{$theme['name']} - Downloaded {$theme['downloadcount']} times' /></a><br/>
							<a href='{$theme['testrun']}' target='_blank'>Test Run</a> | <a href='#' onclick='return false;' target='_blank'>Install</a>
						</span></div>\n";
				}
				return $ret;
			}//end html
			//Format for.. JSSI? XML?
			return;
		} elseif('plugins' == $item){
			
		}
	}
	/*** THEME SEARCH FUNCTIONS ****/
	/** FEATURED THEEMS */
	function getThemesFeatured($returnType = 'array'){
		$themes = wp_cache_get('wpupdate_ThemesFeatured', 'wpupdate');
		if( !$themes ){
			$snoopy = new Snoopy();
			$snoopy->fetch('http://wordpress.org/extend/themes/themes.php');
			$themes = $this->parseThemesFeaturedHTML($snoopy->results);
			wp_cache_set('wpupdate_ThemesFeatured', $themes, 'wpupdate', 86400); //24*60*60=86400
		}
		if( 'array' == $returnType) return $themes;
		return false;
	}
	function parseThemesFeaturedHTML($html){
		preg_match_all('#<div class="featured" id="(.*)">#',$html,$ids);
		preg_match_all('#<p><img src="(.*?)" width="420" height="350" /></p>#',$html,$img);
		preg_match_all('#<h1>(.*?)</h1>#',$html,$name);
		preg_match_all('#<p>Design by <a href="(.*?)" target="_parent">(.*?)</a></p>#',$html,$author);
		preg_match_all('#<p class="download"><a href="(.*?)">Download</a></p>#', $html, $download);
		
		$ret = array();
		for($i=0; $i < count( $ids[1] ); $i++){
			$ret[] = array(
						'id' 		=> $ids[1][$i],
						'thumbnail' => 'http://wordpress.org' . $img[1][$i],
						'name' 		=> $name[1][$i],
						'author' 	=> $author[2][$i],
				   'authorhomepage' => $author[1][$i],
						'download' 	=> $download[1][$i]
						);
		}
		return $ret;
	}
	/** THEME SEARCH **/
	function searchThemes($tags,$page,$maxPage=5){
		if( $page > $maxPage)
			return false;
			
		$urlpart = array();
		foreach($_POST as $postname=>$postvalue){
			if(is_array($postvalue)){
				foreach($postvalue as $subpostvalue)
					$urlpart[] = urlencode($postname.'[]').'='.urlencode($subpostvalue);
			} else {
				$urlpart[] = urlencode($postname).'='.urlencode($postvalue);
			}
		}
		$url = implode('&',$urlpart);
		$url = 'http://themes.wordpress.net/?' . $url . '&paged=' . $page;

		$snoopy = new Snoopy();
		$snoopy->fetch($url);
		$html = $snoopy->results;
		$themes = $this->parseThemeHTML($html);
		/* Check if this is the last page, if not, grab the next page too */
		if ( preg_match('#<div id="bottompagenav">.*>(\d+)</a></p></div>#',$html,$pages) ){
			$themes2 = $this->searchThemes($tags,++$page);
			if( $themes2 )
				$themes = array_merge($themes, $themes2 );
		}
		return $themes;
	}
	function parseThemeHTML($html){
		preg_match_all('#<a href="(.*?)" rel="bookmark" title="(.*?)"><img src="/snapshots/(\d+?)-thumb.jpg" alt="\2" /></a>#i',$html,$mat1);
		preg_match_all('#Download \((\d+)\)#i',$html,$mat2);
		$ret = array();
		
		$totalItems = count($mat2[1]);
		
		if( false == $mat1 )
			return false;
		
		for($i=0;$i<$totalItems;$i++){
			$id = $mat1[3][$i];
			$ret[] = array(
							'name'=>$mat1[2][$i],
							'url'=>$mat1[1][$i],
							'id'=>$id,
							'download'=>'http://themes.wordpress.net/download.php?theme='.$id,
							'downloadcount'=>$mat2[1][$i],
							'snapshot'=> array(
										'thumb'=>'http://themes.wordpress.net/snapshots/'.$id.'-thumb.jpg',
										'medium'=>'http://themes.wordpress.net/snapshots/'.$id.'-medium.jpg',
										'big'=>'http://themes.wordpress.net/snapshots/'.$id.'-big.jpg'
										),
							'testrun'=>'http://themes.wordpress.net/testrun/?wptheme='.$id
							);
		}
		return $ret;
	}
	/*** PLUGIN SEARCH FUNCTIONS ***/
	
	/** PLUGIN TAG FUNCTIONS **/
	function getPluginSearchTags(){
		$tags = wp_cache_get('wpupdate_PluginSearchTags', 'wpupdate');
		if(!$tags){
			$snoopy = new Snoopy();
			$snoopy->fetch('http://wordpress.org/extend/plugins/tags/');
			$tags = $this->parseTagHTML($snoopy->results);
			wp_cache_set('wpupdate_PluginSearchTags', $tags, 'wpupdate', 43200); //12*60*60=43200
		}
		return $tags;
	}
	function getPluginsByTag($tag=false,$page){
		if(!$tag) return;
		
	}
	function parseTagHTML($html){
		$ret = array();
		preg_match_all("#<a href='(.*)' title='(\d+) topics' rel='tag' style='font-size: ([\d\.]+)pt;'>(.*)</a>#i",$html,$tags);
				
		for($i=0;$i<count($tags[0]);$i++){
			$ret[] = array(
						'name' => $tags[4][$i],
						'url'  => $tags[1][$i],
						'number'  => $tags[2][$i],
						'pointsize' => $tags[3][$i]
						);
		}
		return $ret;
	}
}
?>