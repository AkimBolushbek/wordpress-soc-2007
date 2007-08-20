<?php
add_filter('wpupdate_themeSearchProviders','wpupdate_themeswordpressnet_search',1);
function wpupdate_themeswordpressnet_search($args){

	$url = wpupdate_themeswordpressnet_searchCreate($args['info']);
	$url = 'http://themes.wordpress.net/?' . $url . '&submit=Show';
	if( $args['info']['page'] > 1)
		$url .= '&paged=' . $args['info']['page'];

	$results = wp_cache_get('wpupdate_searchThemesThemesWordpressNet_'.md5($url), 'wpupdate');

	if( ! $results ){
		$results = array('results'=>array(),'pages'=>0);
		$snoopy = new Snoopy();
		$snoopy->fetch($url);

		preg_match_all('#<a href="(.*?)" rel="bookmark" title="(.*?)"><img src=".*/snapshots/(\d+?)-thumb.jpg"#i',$snoopy->results,$mat1);

		if( ! $mat1 )
			return $args;

		for($i = 0; $i < count($mat1[1]); $i++){
			$id = $mat1[3][$i];
			$results['results'][] = array(
							'name'=>$mat1[2][$i],
							'url'=>$mat1[1][$i],
							'id'=>$id,
							'download'=>'http://themes.wordpress.net/download.php?theme='.$id,
							'snapshot'=> array(
										'thumb'=>'http://s.themes.wordpress.net/snapshots/'.$id.'-thumb.jpg',
										'medium'=>'http://s.themes.wordpress.net/snapshots/'.$id.'-medium.jpg',
										'big'=>'http://s.themes.wordpress.net/snapshots/'.$id.'-big.jpg'
										),
							'testrun'=>'http://themes.wordpress.net/testrun/?wptheme='.$id
							);
		}
		
		//Check the number of pages, If this isnt the last page, change it.
		if ( preg_match('#title="Last &raquo;">(\d+)</a>#',$snoopy->results,$pages) )
			$results['pages'] = (int)$pages[1];
		wp_cache_add('wpupdate_searchThemesThemesWordpressNet_'.md5($url),$results,'wpupdate',21600);
	} //end if ( ! $results )

	//Merge Result set
	$args['results'] = array_merge($args['results'], $results['results']);
	//Check if theres more pages than set
	if( $results['pages'] > $args['info']['pages'] )
		$args['info']['pages'] = $results['pages'];
	return $args;
}

function wpupdate_themeswordpressnet_searchCreate($options){
$tags = array( 	1 => '1 column', 2 => '2 columns',	3 => '3 columns', 4 => '4 columns', 
				6 => 'Red', 7 => 'Green', 8 => 'Blue', 9 => 'Violet', 10 => 'Orange', 
				11 => 'Brown ',	12 => 'Pink', 13 => 'Salmon', 14 => 'Gray', 15 => 'Black',
				16 => 'White', 31 => 'Yellow',
				22 => 'Fixed width', 23 => 'Fluid width',
				24 => 'Plugins required', 25 => 'Widget ready', 26 => 'Options page',
				27 => 'Rounded corners', 28 => 'Left sidebar', 29 => 'Right sidebar',
				30 => 'No images' ); //17~21 do not exist.
	$string = array();
	foreach( (array) $options['searchOptions'] as $name){
		if( false !== ( $id = array_search($name,$tags) ) )
			$string[] = 'cats%5B%5D='.$id; //cats%5B%5D = cats[]
	}
	//Now for the ordering.
	$string[] = 'sortby=' . $options['sortby'];
	$string[] = 'order=' . $options['order'];
	$string[] = 'andor=' . $options['andor'];
	
	return join('&amp;',$string);
}

add_filter('update_themeSearchOptions','wpupdate_wordpressnet_themeSearchOptions');
function wpupdate_wordpressnet_themeSearchOptions($args){
	//Default Structure
	if( ! isset($args['sections']['Columns']) )
		$args['sections']['Columns'] = array();
	if( ! isset($args['sections']['Colour']) )
		$args['sections']['Colour'] = array();
	if( ! isset($args['sections']['Advanced']) )
		$args['sections']['Advanced'] = array();

	//Columns
	$args['sections']['Columns'][] = "1 column";
	$args['sections']['Columns'][] = "2 columns";
	$args['sections']['Columns'][] = "3 columns";
	$args['sections']['Columns'][] = "4 columns";
	$args['sections']['Columns'][] = "Fixed width";
	$args['sections']['Columns'][] = "Fluid width";

	//Colour
	$args['sections']['Colour'][] = "Red";
	$args['sections']['Colour'][] = "Green";
	$args['sections']['Colour'][] = "Blue";
	$args['sections']['Colour'][] = "Violet";
	$args['sections']['Colour'][] = "Orange";
	$args['sections']['Colour'][] = "Brown";
	$args['sections']['Colour'][] = "Pink";
	$args['sections']['Colour'][] = "Salmon";
	$args['sections']['Colour'][] = "Gray";
	$args['sections']['Colour'][] = "Black";
	$args['sections']['Colour'][] = "White";
	$args['sections']['Colour'][] = "Yellow";

	//Advanced
	$args['sections']['Advanced'][] = "Plugins required";
	$args['sections']['Advanced'][] = "Widget ready";
	$args['sections']['Advanced'][] = "Options page";
	$args['sections']['Advanced'][] = "Rounded corners";
	$args['sections']['Advanced'][] = "Left sidebar";
	$args['sections']['Advanced'][] = "Right sidebar";
	$args['sections']['Advanced'][] = "No images";

	return $args;
}
?>