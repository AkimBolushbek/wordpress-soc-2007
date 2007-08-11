<?php
/* Notes:
	Some items have been changed from the default wordpress Install:
		- removed is_readable check; Unneeded IO operations, if error occures while opening file, return false to signify it, 
		- added Update URI: entry
		- maybe a few other items
*/
function wpupdate_get_plugin_data( $plugin_file ) {
	$plugin_data = @implode( '', @file( $plugin_file ));
	if(!$plugin_data)
		return;
	preg_match( '|Plugin Name:(.*)|i', $plugin_data, $plugin_name );
	preg_match( '|Plugin URI:(.*)|i', $plugin_data, $plugin_uri );
	preg_match( '|Description:(.*)|i', $plugin_data, $description );
	preg_match( '|Author:(.*)|i', $plugin_data, $author_name );
	preg_match( '|Author URI:(.*)|i', $plugin_data, $author_uri );
	if ( preg_match( '|Version:(.*)|i', $plugin_data, $version ))
		$version = trim( $version[1] );
	else
		$version = '';
	if ( preg_match( '|Update URI:(.*)|i', $plugin_data, $update_uri ))
		$update_uri = trim( $update_uri[1] );
	else
		$update_uri = '';

	$description = wptexturize( trim( $description[1] ));

	$name = $plugin_name[1];
	$name = trim( $name );
	$plugin = $name;
	if ('' != $plugin_uri[1] && '' != $name ) {
		$plugin = '<a href="' . trim( $plugin_uri[1] ) . '" title="'.__( 'Visit plugin homepage' ).'">'.$plugin.'</a>';
	}

	if ('' == $author_uri[1] ) {
		$author = trim( $author_name[1] );
	} else {
		$author = '<a href="' . trim( $author_uri[1] ) . '" title="'.__( 'Visit author homepage' ).'">' . trim( $author_name[1] ) . '</a>';
	}

	return array ('Name' => $name, 'Title' => $plugin, 'Description' => $description, 'Author' => $author, 'Version' => $version, 'Update' => $update_uri );
}

function wpupdate_get_plugins($plugin_root='') {
	global $wp_plugins;

	if ( isset( $wp_plugins ) ) {
		return $wp_plugins;
	}

	$wp_plugins = array ();
	if( empty($plugin_root) )
		$plugin_root = ABSPATH . PLUGINDIR;

	// Files in wp-content/plugins directory
	$plugins_dir = @ dir( $plugin_root);
	if ( $plugins_dir ) {
		while (($file = $plugins_dir->read() ) !== false ) {
			if ( '.' == $file || '..' == $file )
				continue;
			if ( is_dir( $plugin_root.'/'.$file ) ) {
				$plugins_subdir = @ dir( $plugin_root.'/'.$file );
				if ( $plugins_subdir ){
					while (($subfile = $plugins_subdir->read() ) !== false ){
						if ( preg_match( '|\.php$|', $subfile ) )
							$plugin_files[] = "$file/$subfile";
					}
				}
			} else {
				if ( preg_match( '|\.php$|', $file ))
					$plugin_files[] = $file;
			}
		}
	}

	if ( !$plugins_dir || !$plugin_files )
		return $wp_plugins;

	foreach ( $plugin_files as $plugin_file ) {

		$plugin_data = wpupdate_get_plugin_data( "$plugin_root/$plugin_file" );

		if ( empty($plugin_data) || empty ( $plugin_data['Name'] ) )
			continue;

		$wp_plugins[plugin_basename( $plugin_file )] = $plugin_data;
	}

	uasort( $wp_plugins, create_function( '$a, $b', 'return strnatcasecmp( $a["Name"], $b["Name"] );' ));

	return $wp_plugins;
}


function wpupdate_themeSearchHTML($theme){
	return "&nbsp;<div class='themeinfo'>
				<span>
					<a href='{$theme['url']}' title='{$theme['name']}' target='_blank'>{$theme['name']}<br />
					<img src='{$theme['snapshot']['thumb']}' alt='{$theme['name']} - Downloaded {$theme['downloadcount']} times' title='{$theme['name']} - Downloaded {$theme['downloadcount']} times' /></a><br/>
					<a href='{$theme['testrun']}' target='_blank'>".__('Test Run')."</a> | <a href='themes.php?page=wp-update/wp-update-themes-install.php&step=2&url=".urlencode($theme['download'])."' target='_blank'>".__('Install')."</a>
				</span>
			</div>\n";
}
function wpupdate_pluginSearchHTML($plugin,$wordwrap=25){
	return '<div class="plugin"><span>
			<h3>'.$plugin['Name'].'</h3>
				<p>
				' . wordwrap($plugin['Desc'],$wordwrap,"<br/>\n") . '
				</p>
				<p>
				<a href="plugins.php?page=wp-update/wp-update-plugins-install.php&wp-id='.urlencode($plugin['Id']).'">' . __('Install') . '</a> 
				<a href="'.$plugin['PluginHome'].'" target="_blank">WordPress.Org</a>
				</p>
		</span></div> &nbsp; ';
}
function succeeded($result=false,$args=''){
	$defaults = array(
		'before' => ' ', 'after' => '',
		'true' => 'OK', 'false' => 'FAILED',
		'true-colour' => 'green', 'false-colour' => 'red'
		);
	$r = wp_parse_args( $args, $defaults );

	if( $result )
		return $r['before'] . '<span style="color:' . $r['true-colour'] . '">' . __($r['true']) . '</span>' . $r['after'];
	else
		return $r['before'] . '<span style="color:' . $r['false-colour'] . '">' . __($r['false']) . '</span>' . $r['after'];
}
function wpupdate_url_to_file($url=false){
	//WARNING: The file is not automatically deleted, The script must unlink() the file.
	if( ! $url )
		return false;

	$tmpfname = tempnam('/tmp', 'wpupdate');
	if( ! $tmpfname )
		return false;

	$handle = fopen($tmpfname, 'w');
	if( ! $handle )
		return false;

	require_once( ABSPATH . 'wp-includes/class-snoopy.php' );
	$snoopy = new Snoopy();
	$snoopy->fetch($url);
	
	fwrite($handle, $snoopy->results);
	fclose($handle);

	return $tmpfname;
}
function wpupdate_generate_tagcloud($tags=false,$args=''){
	$defaults = array(
		'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 45,
		'format' => 'flat', 'orderby' => 'name', 'order' => 'ASC', 'link'=>'%s'
	);

	if ( !$tags )
		return;

	$args = wp_parse_args( $args, $defaults );
	
	extract($args);

	$counts = $tag_links = array();
	foreach ( (array) $tags as $name => $count ) {
		$counts[$name] = $count;
		$tag_links[$name] = sprintf($link, $name);
	}

	$min_count = min($counts);
	$spread = max($counts) - $min_count;
	if ( $spread <= 0 )
		$spread = 1;
	$font_spread = $largest - $smallest;
	if ( $font_spread <= 0 )
		$font_spread = 1;
	$font_step = $font_spread / $spread;

	// SQL cannot save you; this is a second (potentially different) sort on a subset of data.
	if ( 'name' == $orderby )
		uksort($counts, 'strnatcasecmp');
	else
		asort($counts);

	if ( 'DESC' == $order )
		$counts = array_reverse( $counts, true );

	$a = array();

	foreach ( $counts as $tag => $count ) {
		$tag_link = $tag_links[$tag];//clean_url($tag_links[$tag]);
		$tag = str_replace(' ', '&nbsp;', wp_specialchars( $tag ));
		$a[] = "<a href='$tag_link' title='" . attribute_escape( sprintf( __('%d items'), $count ) ) . "' style='font-size: " .
			( $smallest + ( ( $count - $min_count ) * $font_step ) )
			. "$unit;'>$tag</a>";
	}

	switch ( $format ) :
	case 'array' :
		$return =& $a;
		break;
	case 'list' :
		$return = "<ul class='wp-tag-cloud'>\n\t<li>";
		$return .= join("</li>\n\t<li>", $a);
		$return .= "</li>\n</ul>\n";
		break;
	default :
		$return = join("\n", $a);
		break;
	endswitch;

	return apply_filters( 'wpupdate_generate_tag_cloud', $return, $tags, $args );
}

function folder_diff($folder1, $folder2){
	global $wp_filesystem;
	if( ! $wp_filesystem || ! is_object($wp_filesystem) )
		return false;
	//Ok.
	
	$Files = array();
		
	$folder1Listing = $wp_filesystem->dirlist($folder1,false,false);
	$folder2Listing = $wp_filesystem->dirlist($folder2,false,false);

	//if( empty($folder1Listing) || empty($folder2Listing) )
	//	return false;
	
	foreach((array)$folder1Listing as $fileName => $fileItem){
		if( 'file' == $fileItem['type'] ){
			if( !isset($folder2Listing[ $fileName ]) ){
				//File is new
				$fileItem['status'] = 'new';
				$Files[ $fileName ] = $fileItem;
			} elseif( $fileItem['size'] !== $folder2Listing[ $fileName ]['size'] ){
				//File has changed
				$fileItem['status'] = 'changed';
				$fileItem['oldsize'] = $folder2Listing[ $fileName ]['size'];
				$Files[ $fileName ] = $fileItem;
			}
		} elseif( 'folder' == $fileItem['type'] ){
			if( ! isset($folder2Listing[ $fileName ]) ){
				$fileItem['status'] = 'deleted';
				$Files[ $fileName ] = $fileItem;
				continue;
			}
			$items = folder_diff($folder1 . '/' . $fileName, $folder2 . '/' . $fileName);
			if( !$items || empty($items) ){
				$fileItem['status'] = 'deleted';
				$Files[ $fileName ] = $fileItem;
			} else {
				$Files[ $fileName ]['status'] = 'changed'; //unset as deleted.
				foreach( $items as $folderFile => $folderEntry){
					$Files[ $fileName . '/' . $folderFile ] = $folderEntry;
				}
			}
		}
	}
	foreach($folder2Listing as $fileName => $fileItem){
		if( isset( $Files[ $fileName ] ) )
			continue; //If the file is allready accounted for, skip
		if( 'file' == $fileItem['type'] ){
			if( !isset($folder1Listing[ $fileName ]) ){
				//File is deleted
				$fileItem['status'] = 'deleted';
				$Files[ $fileName ] = $fileItem;
			} else {
				//File is the same
				$fileItem['status'] = 'same';
				$Files[ $fileName ] = $fileItem;
			}
		} elseif( 'folder' == $fileItem['type'] ){
			//If the folder wasnt delt with before (Which would've placed it in the $Files array, then it must be deleted
			$fileItem['status'] = 'deleted';
			$Files[ $fileName ] = $fileItem;
		}
	}
	return $Files;
}//end function

?>