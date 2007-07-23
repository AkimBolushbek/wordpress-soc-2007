<?php

add_filter('wpupdate_pluginSearchProviders','wpupdate_wordpressorg_search',1);
/**
 * Searches WordPress.org/extend/plugins/ for a term
 * @param mixed $args Search results Struct.
 * @return mixed $args Search results Struct.
 */
function wpupdate_wordpressorg_search($args){
	$term = join(' ',$args['info']['terms']);
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
						'Tags'		=> array($term),
						'Rating'	=> ''
						);
		}
	}
	wp_cache_set('wpupdate_searchPluginsWordpressOrg_'.rawurlencode($term), $results, 'wpupdate', 21600); //6*60*60=21600
	
	$args['results'] = array_merge($args['results'],$results);
	return $args;
} 

add_filter('wpupdate_pluginTagList','wpupdate_wordpressorg_taglist');
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

add_filter('wpupdate_pluginTagSearch','wpupdate_wordpressorg_tagsearch');
function wpupdate_wordpressorg_tagsearch($args){

		$results = wp_cache_get('wpupdate_pluginSearchTag_wordpressorg_'.$args['info']['terms'].'-'.$args['info']['page'], 'wpupdate');
		if( $results ){
			$args['results'] = array_merge($args['results'],$results);
			return $args;
		}

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

			$results[] = array(
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
		if ( preg_match_all("#<a class='page-numbers' href='/extend/plugins/tags/post/page/(\d+)'>(\d+)</a>#",$snoopy->results,$pages) ){
			$pages = (int)$pages[2][ count($pages[2]) - 1 ];
			if( $pages > $args['info']['pages'] )
				$args['info']['pages'] = $pages;
		}
		wp_cache_set('wpupdate_pluginSearchTag_wordpressorg_'.$results['info']['terms'].'-'.$results['info']['page'], $results, 'wpupdate', 21600);
		
		$args['results'] = array_merge($args['results'],$results);
		return $args;
}

?>