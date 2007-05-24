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
				foreach( (array)$items as $theme){
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
	/*** PLUGIN FUNCTIONS ***/
	
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
	function getPluginsByTag($tag=false,$page=1){
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
	
	/** PLUGIN SEARCH FUNCTIONS **/
	function searchPlugins($term){
		$searchresults = wp_cache_get('wpupdate_search_'.rawurlencode($term), 'wpupdate');
		if( !$searchresults ){
			$url = 'http://wordpress.org/extend/plugins/search.php?q='.rawurlencode($term);
			$snoopy = new Snoopy();
			$snoopy->fetch($url);
			$searchresults = array();
			preg_match_all('#<h2>(Plugin title matches|Relevant plugins)</h2>(.*?)</ol>#ims',$snoopy->results,$mat);
			for( $i=0; $i < count($mat[1]); $i++){
				$regex = ('Plugin title matches' == $mat[1][$i]) ? 
							'#<li><h4><a href="(.*?)">(.*?)</a></h4>\n<small><p>(.*?)</p></small>#ims' : 
							'#<li><h4><a href="(.*?)">(.*?)</a></h4>\n<p>(.*?)</p>#ims';
							
				preg_match_all($regex,$mat[2][$i],$matPlugins);
				
				$type = ('Plugin title matches' == $mat[1][$i]) ? 'titlematch' : 'relevant';
				
				for( $j=0; $j < count($matPlugins[1]); $j++){
					$searchresults[ $type ][] = array('Uri'=>$matPlugins[1][$j], 'Name'=>$matPlugins[2][$j], 'Desc'=>$matPlugins[3][$j]);
				}
			}
			wp_cache_set('wpupdate_search_'.rawurlencode($term), $searchresults, 'wpupdate', 21600); //6*60*60=21600
		}
		return $searchresults;
	}
	
	/** PLUGIN UPDATE FUNCTIONS **/
	function checkPluginUpdate($pluginfile=false,$skipcache=false,$forcecheck=false){
		if( ! $pluginfile ) return array('Errors'=>array('Invalid File'));
		$pluginUpdateInfo = false;
		
		if( ! $skipcache ) $pluginUpdateInfo = wp_cache_get('wpupdate_'.$pluginfile, 'wpupdate');

		if( ! $pluginUpdateInfo && ! $forcecheck ) return array('Errors'=>array('Not Cached'));
		
		$pluginData = wpupdate_get_plugin_data(ABSPATH . PLUGINDIR . '/' . $pluginfile);
		
		if( ! $pluginUpdateInfo || $forcecheck ){
			if ( '' != $pluginData['Update'] && get_option('update_location_custom') ){
				//We have a custom update URL.
				$pluginUpdateInfo = $this->checkPluginUpdateCustom($pluginfile,$pluginData);
			}
			//Else, We check wordpress.org.. 
			if( !$pluginUpdateInfo && get_option('update_location_wordpressorg') ){
				//Find the plugin:
				$plugins = $this->searchPlugins($pluginData['Name']);
				if( isset($plugins['titlematch']) ){
					foreach( (array)$plugins['titlematch'] as $result){
						if( 0 === strcasecmp($result['Name'],$pluginData['Name']) ){
							//Return information:
							$pluginUpdateInfo = $this->checkPluginUpdateWordpressOrg($pluginData,$result);
						}
					}
				}
				if( !$pluginUpdateInfo && isset($plugins['relevant']) ){
					foreach( (array)$plugins['relevant'] as $result){
						if( 0 === strcasecmp($result['Name'],$pluginData['Name']) ){
							//return information:
							$pluginUpdateInfo = $this->checkPluginUpdateWordpressOrg($pluginData,$result);
						}
					}
				}
			}//end get_option('update_location_wordpressorg')
			
			//Else, We check wp-plugins.net
			if( !$pluginUpdateInfo && get_option('update_location_wppluginsnet') )
				$pluginUpdateInfo = $this->checkPluginUpdateWpPluginsNet($pluginData);
			
			//Update cache:
			//If Expire is not set, or Expire is not valid
			if( !empty($pluginUpdateInfo) && !isset($pluginUpdateInfo['Expire']) && ! (int) $pluginUpdateInfo['Expire'] > 0) $pluginUpdateInfo['Expire'] = 7*24*60*60;
			wp_cache_set('wpupdate_'.$pluginfile, $pluginUpdateInfo, 'wpupdate', $pluginUpdateInfo['Expire']);
		}// get cache
		
		if( !$pluginUpdateInfo || !$pluginUpdateInfo['Version'] ){
			//We cant help with this particular plugin as it doesnt specify a version.
			return array('Errors'=>array('Not Compatible'));
		}

		if( version_compare($pluginUpdateInfo['Version'] , $pluginData['Version'], '>') ){
			//Theres a new version available!, Now, Check its Requirements.
			$pluginCompatible = true;
			$errors = array();
			foreach((array)$pluginUpdateInfo['Requirements'] as $reqInfo){

				switch($reqInfo['Name']){
					case "WordPress":
						if( isset($reqInfo['Min']) ){
							if( version_compare( $wp_version, $reqInfo['Min'], '>=' ) ){
								$pluginCompatible = false;
								$errors[] = 'Requires WordPress '.$reqInfo['Min'];
							}
						}
						if( isset($reqInfo['Tested']) ){
							if( version_compare( $wp_version, $reqInfo['Tested'], '<=' ) ){
								$errors[] = 'Only tested Upto WordPress '.$reqInfo['Tested'];
							}
						}
						break;
					case "PHP":
						//May have to keep in mind early PHP5 releases may not include all PHP4 functions/bugfixes
						if( isset($reqInfo['Min']) ){
							if( version_compare( phpversion(), $reqInfo['Min'], '>=' ) ){
								$pluginCompatible = false;
								$errors[] = 'Requires PHP '.$reqInfo['Min'];
							}
						}
						if( isset($reqInfo['Tested']) ){
							if( version_compare( phpversion(), $reqInfo['Tested'], '<=' ) ){
								$errors[] = 'Only tested Upto PHP '.$reqInfo['Tested'];
							}
						}
						break;
					case "MySQL":
						if( isset($reqInfo['Min']) ){
							if( version_compare( mysql_get_server_info(), $reqInfo['Min'], '>=' ) ){
								$pluginCompatible = false;
								$errors[] = 'Requires MySQL '.$reqInfo['Min'];
							}
						}
						if( isset($reqInfo['Tested']) ){
							if( version_compare( mysql_get_server_info(), $reqInfo['Tested'], '<=' ) ){
								$errors[] = 'Only tested Upto MySQL '.$reqInfo['Tested'];
							}
						}
						break;
					case "Plugins":
					//TODO
						break;
					case "PHPExt":
					//TODO
						break;
					default:
				} //end switch()
			} //end foreach()
			if( !empty($errors) ) return array('Update'=>true,'Compatible'=>$pluginCompatible,'Version'=>$pluginUpdateInfo['Version'],'Errors'=>$errors);
			return array('Update'=>true,'Compatible'=>$pluginCompatible,'Version'=>$pluginUpdateInfo['Version']);
		} else {
			//The currently installed version is the latest availaable.
			return array('Update'=>false);
		}
	}
	function checkPluginUpdateWpPluginsNet($pluginData){
		$plugin = false;
		$snoopy = new Snoopy();
		$snoopy->fetch('http://wp-plugins.net/get_plugin_data.php?wp_version=any&filter='.$pluginData['Name']);
		if( ! $snoopy->results )
			return false;
		$WPInfo = unserialize($snoopy->results);
		foreach( (array)$WPInfo as $plugin){
			if( 0 === strcasecmp($plugin['plugin_name'],$pluginData['Name']) ){
				//We have a match.
				$plugin = array(
						'Name' 		=>	$plugin['plugin_name'],
						'Version'	=>	$plugin['version_major'].'.'.$plugin['version_minor'],
						'LastUpdate'=>	$plugin['date_updated'],
						'Download'	=>	( '' != $plugin['oneclick_url']) ? $plugin['oneclick_url'] : $plugin['download_url'],
						'Author'	=>	$plugin['author'],
						'WPAuthor'	=>	'',
						'AuthorHome'=>	$plugin['author_url'],
						'PluginHome'=>	$plugin['plugin_url'],
						'Rating'	=>	0,
						'Tags'		=>	array($plugin['top_cat'],$plugin['cat_name'],$plugin['parent_name'],$plugin['dir_name']),
						'Related'	=>	array(),
						'Requirements' => array(),
						'Expire'	=> 7*24*60*60
						);
						//Expire in 1 week
				break;
			}
		}
		if( $plugin )
			return $plugin;
		else 
			return false;
	}

	function checkPluginUpdateWordpressOrg($pluginData,$wordpressInfo){
		if ( ! $wordpressInfo['Uri'] ) return false;
		$snoopy = new Snoopy();
		$snoopy->fetch($wordpressInfo['Uri']);
		preg_match('#<h2>(.*)</h2>#',$snoopy->results,$name);
		preg_match('#<strong>Version:<\/strong> ([\d\.]+)#',$snoopy->results,$version);
		preg_match('#<strong>Last Updated:</strong> ([\d\-]+)#',$snoopy->results,$lastupdate);
		preg_match("#<a href='(.*?)'>Download#",$snoopy->results,$download);
		preg_match("#<a href='(.*?)'>Author Homepage#",$snoopy->results,$authorhomepage);
		preg_match("#<\/li>.*<li><a href='(.*?)'>Plugin Homepage#",$snoopy->results,$pluginhomepage);
		preg_match("#<a href='http:\/\/wordpress.org\/extend\/plugins\/profile\/(.*?)'>(.*?)<\/a>#",$snoopy->results,$authordetails);
		preg_match('#star-rating" style="width: ([\d\.]+)px"#',$snoopy->results,$rating);
		preg_match('#<div id="plugin-tags">(.*?)</div>#s',$snoopy->results,$tag_section);
			preg_match_all("#<a href='http:\/\/wordpress.org\/extend\/plugins\/tags\/(.*?)'>(.*?)<\/a>#",$tag_section[1],$tags);
		preg_match('#<div id="related">(.*?)</div>#s',$snoopy->results,$related_section);
			preg_match_all("#<a href='http:\/\/wordpress.org\/extend\/plugins\/plugin\/(.*?)\/'>(.*?)<\/a>#",$related_section[1],$relatedplugins);
		
		preg_match('#<li><strong>Requires WordPress Version:</strong> ([\d\.]+)#', $snoopy->results, $req_version);
		preg_match('#<li><strong>Compatible up to:</strong> ([\d\.]+)#',$snoopy->results, $compat_version);
		
		if ( !empty($req_version) || !empty($compat_version) ){
			$wordpress['Name'] = 'WordPress';
			if( !empty($req_version) ) $wordpress['Min'] = $req_version[1];
			if( !empty($compat_version) ) $wordpress['Tested'] = $compat_version[1];
			$requirements[] = $wordpress;
		}
	
		for($i=0; $i<count($tags[0]); $i++)
			$final_tags[ $tags[1][$i] ] = $tags[2][$i];
		
		for($i=0; $i<count($relatedplugins[0]); $i++)
			$final_related[ $relatedplugins[1][$i] ] = $relatedplugins[2][$i];
		
		$plugin = array(
					'Name' 		=>	trim($name[1]),
					'Version'	=>	trim($version[1]),
					'LastUpdate'=>	trim($lastupdate[1]),
					'Download'	=>	trim($download[1]),
					'Author'	=>	trim($authordetails[2]),
					'WPAuthor'	=>	trim($authordetails[1]),
					'AuthorHome'=>	trim($authorhomepage[1]),
					'PluginHome'=>	trim($pluginhomepage[1]),
					'Rating'	=>	trim($rating[1]),
					'Tags'		=>	$final_tags,
					'Related'	=>	$final_related,
					'Requirements' => $requirements,
					'Expire'	=> 7*24*60*60
					);
		return $plugin;
	}
	function checkPluginUpdateCustom($pluginfile,$plugindata){
		$url = $plugindata['Update'];
		$snoopy = new Snoopy();
		$snoopy->fetch($url);
		//TODO: If Is serialised data, then return, else return null
		return unserialize($snoopy->results);
	}
}
?>