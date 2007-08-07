<?php

add_filter('wpupdate_pluginSearchProviders','wpupdate_wordpressorg_search',1);
/**
 * Searches WordPress.org/extend/plugins/ for a term
 * @param mixed $args Search results Struct.
 * @return mixed $args Search results Struct.
 */
function wpupdate_wordpressorg_search($args){
	if( is_array($args['info']['terms']) )
		$term = join(' ',$args['info']['terms']);
	else
		$term = $args['info']['terms'];
	$results = wp_cache_get('wpupdate_searchPluginsWordpressOrg_'.rawurlencode($term), 'wpupdate');
	if( $results ){
		$args['results'] = array_merge($args['results'],$results);
		return $args;
	}
	$results = array();
	$url = 'http://wordpress.org/extend/plugins/search.php?q='.rawurlencode($term);
	$snoopy = new Snoopy();
	$snoopy->fetch($url);

	preg_match_all('#<h2>(Plugin title matches|Relevant plugins)</h2>(.*?)</ol>#ims',$snoopy->results,$mat);
	for( $i=0; $i < count($mat[1]); $i++){
		$regex = ('Plugin title matches' == $mat[1][$i]) ? 
					'#<li><h4><a href="(.*?)">(.*?)</a></h4>\n<small>(.*?)</small>#ims' : 
					'#<li><h4><a href="(.*?)">(.*?)</a></h4>\n<p>(.*?)</p>#ims';

		preg_match_all($regex,$mat[2][$i],$matPlugins);

		for( $j=0; $j < count($matPlugins[1]); $j++){
			preg_match('#plugins/(.*?)/#',$matPlugins[1][$j],$wordpressId);
			$name = trim($matPlugins[2][$j],'<p></p>');
			$found = false;
			foreach($results as $res){
				if( $name == $res['Name'] ){
					$found = true;
					break;
				}
			}
			if( ! $found )
				$results[] = array(
						'Name' 		=> $name,
						'Desc'		=> trim($matPlugins[3][$j]),
						'Version' 	=> '',
						'LastUpdate'=> '',
						'Id'		=> $wordpressId[1],
						'Download'	=> 'http://downloads.wordpress.org/plugin/' . $wordpressId[1] . '.zip',
						'PluginHome'=> trim($matPlugins[1][$j]),
						'UpdateURL' => trim($matPlugins[1][$j]),
						'Tags'		=> array($term),
						'Rating'	=> ''
						);
		}
	}
	wp_cache_set('wpupdate_searchPluginsWordpressOrg_'.rawurlencode($term), $results, 'wpupdate', 21600); //6*60*60=21600
	
	$args['results'] = array_merge($args['results'],$results);
	return $args;
} 

add_filter('wpupdate_pluginTagList','wpupdate_wordpressorg_taglist',1);
function wpupdate_wordpressorg_taglist($tags){

	$arrTags = wp_cache_get('wpupdate_searchPluginsWordpressOrg_TagList', 'wpupdate');
	if( ! $arrTags ) {
		$snoopy = new Snoopy();
		$snoopy->fetch('http://wordpress.org/extend/plugins/tags/');
		preg_match_all("#<a href='(.*)' title='(\d+) topics' rel='tag' style='font-size: ([\d\.]+)pt;'>(.*)</a>#i",$snoopy->results,$arrTags);
		wp_cache_set('wpupdate_searchPluginsWordpressOrg_TagList', $arrTags, 'wpupdate', 43200); //12*60*60=43200
	}

	for( $i = 0; $i < count($arrTags[0]); $i++){
		$name = $arrTags[4][$i];
		if( ! isset($tags[ $name ]) )
			$tags[ $name ] = $arrTags[2][$i];
		else
			$tags[ $name ] += $arrTags[2][$i];
	}

	return $tags;
}

add_filter('wpupdate_pluginTagSearch','wpupdate_wordpressorg_tagsearch',1);
function wpupdate_wordpressorg_tagsearch($args){
	$results = wp_cache_get('wpupdate_pluginSearchTag_wordpressorg_'.$args['info']['terms'].'-'.$args['info']['page'], 'wpupdate');

	if( ! $results ){
		$url = 'http://wordpress.org/extend/plugins/tags/' . $args['info']['terms'];
		if( $args['info']['page'] > 1 ) $url .= '/page/' . $args['info']['page'];
	
		$snoopy = new Snoopy();
		$snoopy->fetch($url);
		
		$results = array();
	
		preg_match_all('#<h3><a href="(.*?)">(.*?)</a></h3>(.*?)<ul class="plugin-meta">#ims',$snoopy->results,$plugindetails);
		preg_match_all('#Version</span> (.*?)</li>#i',$snoopy->results,$version);
		preg_match_all('#Updated</span> (.*?)</li>#i',$snoopy->results,$updated);
		preg_match_all('#Downloads</span> (.*?)</li>#i',$snoopy->results,$downloads);
		preg_match_all('#star-rating" style="width: (.*?)px#i',$snoopy->results,$rating);
		
		if( empty($plugindetails[1]) )
			return $args;
	
		for( $i = 0; $i < count($plugindetails[0]); $i++){
			preg_match('#plugins/(.*?)/$#',$plugindetails[1][$i],$wordpressId);
	
			$results['results'][] = array(
									'Name' 		=> trim($plugindetails[2][$i]),
									'Desc'		=> trim($plugindetails[3][$i]),
									'Version' 	=> trim($version[1][$i]),
									'LastUpdate'=> trim($updated[1][$i]),
									'Id'=> $wordpressId[1],
									'Download'	=> 'http://downloads.wordpress.org/plugin/' . $wordpressId[1] . '.zip',
									'PluginHome'=> trim($plugindetails[1][$i]),
									'Tags'		=> array($tag),
									'Rating'	=> trim($rating[1][$i])
									);
		}
		if ( preg_match_all("!<a class='page-numbers' href='/extend/plugins/tags/spam/page/(\d+)'>\\1</a>!ims",$snoopy->results,$pages) )
			$results['info']['pages'] = (int)$pages[1][ count($pages[1]) - 1 ];

		wp_cache_set('wpupdate_pluginSearchTag_wordpressorg_'.$results['info']['terms'].'-'.$results['info']['page'], $results, 'wpupdate', 21600);
	} //end if( ! $results);

	$args['results'] = array_merge($args['results'],$results['results']);
	if( $results['info']['pages'] > $args['info']['pages'] )
		$args['info']['pages'] = $results['info']['pages'];

	return $args;
}

add_filter('wpupdate_checkPluginUpdate-wordpress.org','checkPluginUpdateWordpressOrg');
/**
 * Parses the WordPress.org plugin page for an individual plugins details
 *
 * @param string $url the URL of the Plugin page to parse
 * @return mixed array of Plugin details
 * 
 * @TODO Caching
 */
function checkPluginUpdateWordpressOrg($url){
	if ( ! $url ) return false;
	
	if ( false !== strpos($id,'http://') ){
		$url = $id;
		preg_match('!plugins/(.*?)/!',$id,$_id);
		$id = $_id[1];
	}

	$snoopy = new Snoopy();
	$snoopy->fetch($url);
	preg_match('#<h2>(.*)</h2>#',$snoopy->results,$name);
	preg_match('#<strong>Version:<\/strong> ([\.\d]+?)<\/li>#',$snoopy->results,$version);
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
		$wordpress = array('Name' => 'WordPress',
						   'Type' => 'WordPress');
		if( !empty($req_version) ) $wordpress['Min'] = $req_version[1];
		if( !empty($compat_version) ) $wordpress['Tested'] = $compat_version[1];
		$requirements[] = $wordpress;
	}

	for($i=0; $i<count($tags[0]); $i++)
		$final_tags[ $tags[1][$i] ] = $tags[2][$i];
	
	for($i=0; $i<count($relatedplugins[0]); $i++)
		$final_related[ $relatedplugins[1][$i] ] = $relatedplugins[2][$i];
	
	return array(
				'Name' 		=>	trim($name[1]),
				'Version'	=>	trim($version[1]),
				'LastUpdate'=>	trim($lastupdate[1]),
				'Id'		=> 	$id,
				'Download'	=>	trim($download[1]),
				'Author'	=>	trim($authordetails[2]),
				'AuthorHome'=>	trim($authorhomepage[1]),
				'PluginHome'=>	trim($pluginhomepage[1]),
				'UpdateURL' =>  trim($pluginhomepage[1]),
				'Rating'	=>	trim($rating[1]),
				'Tags'		=>	$final_tags,
				'Related'	=>	$final_related,
				'Requirements' => $requirements,
				'Expire'	=> 7*24*60*60
				);
}
add_filter('wpupdate_themesFeatured','wpupdate_WordpressOrg_featured');
function wpupdate_WordpressOrg_featured($args){
	$themes = wp_cache_get('wpupdate_wordpressorg_ThemesFeatured', 'wpupdate');
	if( ! $themes ){
		$themes = array();
		$snoopy = new Snoopy();
		$snoopy->fetch('http://wordpress.org/extend/themes/themes.php');

		preg_match_all('#<div class="featured" id="(.*)">#',$snoopy->results,$ids);
		preg_match_all('#<p><img src="(.*?)" width="420" height="350" /></p>#',$snoopy->results,$img);
		preg_match_all('#<h1>(.*?)</h1>#',$html,$name);
		preg_match_all('#<p>Design by <a href="(.*?)" target="_parent">(.*?)</a></p>#',$snoopy->results,$author);
		preg_match_all('#<p class="download"><a href="(.*?)">Download</a></p>#', $snoopy->results, $download);

		for($i=0; $i < count( $ids[1] ); $i++){
			$themes[] = array(
						'id' 		=> $ids[1][$i],
						'thumbnail' => 'http://wordpress.org' . $img[1][$i],
						'name' 		=> $name[1][$i],
				   'authorhomepage' => $author[1][$i],
						'author' 	=> $author[2][$i],
						'download' 	=> $download[1][$i]
						);
		}

		wp_cache_set('wpupdate_wordpressorg_ThemesFeatured', $themes, 'wpupdate', 86400); //24*60*60=86400
	} // end if (!$themes)
	if( $themes )
		$args['results'] = array_merge($args['results'], (array)$themes);

	return $args;
}
?>