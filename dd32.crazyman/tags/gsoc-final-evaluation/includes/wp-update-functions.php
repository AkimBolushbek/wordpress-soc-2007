<?php
/**
* Modified version of get_plugin_data(), Containes a few minor performance increases as well as extra Metadata items
*
* @param string $plugin_file the file to look for information in
* @return array of Metadata, Null on failuse
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
	if ( preg_match( '|Slug:(.*)|i', $plugin_data, $slug ))
		$slug = trim( $slug[1] );
	else
		$slug = '';

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

	return array ('Name' => $name, 'Title' => $plugin, 'Description' => $description, 'Author' => $author, 'Version' => $version, 'Update' => $update_uri, 'Slug' => $slug );
}

/**
* mofidied version of get_plugins(), few performance increases
*
* @param string $plugin_root the basedirectory to look for plugins in, Defaults to '' which is ABSPATH . PLUGINDIR
* @return array of plugin metadatas.
*/
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

/**
* Prints out a Structure of the Theme information in a block div
*
* @param mixed array $theme the Theme information
* @return string of HTML
*/
function wpupdate_themeSearchHTML($theme){
	return "&nbsp;<div class='themeinfo'>
				<span>
					<a href='{$theme['url']}' title='{$theme['name']}' target='_blank'>{$theme['name']}<br />
					<img src='{$theme['snapshot']['thumb']}' alt='{$theme['name']}' title='{$theme['name']}' /></a><br/>
					<a href='{$theme['testrun']}' target='_blank'>".__('Test Run')."</a> | <a href='" . 
							wp_nonce_url('themes.php?page=wp-update/wp-update-themes-install.php&amp;url='.urlencode($theme['download']),'wpupdate-theme-install') . 
					"' target='_blank'>".__('Install')."</a>
				</span>
			</div>\n";
}
/**
* Prints out a Structure of the Plugin information in a block div
*
* @param mixed array $theme the Theme information
* @param integer $wordwrap the width of text to wrap the plugin information to(To keep teh block width thin)
* @return string of HTML
*/
function wpupdate_pluginSearchHTML($plugin,$wordwrap=25){
	return '<div class="plugin"><span>
			<h3>'.$plugin['Name'].'</h3>
				<p>
				' . wordwrap($plugin['Desc'],$wordwrap,"<br/>\n") . '
				</p>
				<p>
				<a href="' . 
					wp_nonce_url('plugins.php?page=wp-update/wp-update-plugins-install.php&amp;wp-id='.urlencode($plugin['Id']), 'wpupdate-plugin-install')
				.'">' . __('Install') . '</a> <a href="'.$plugin['PluginHome'].'" target="_blank">WordPress.Org</a>
				</p>
		</span></div> &nbsp; ';
}
/**
* Text of if the process suceeds
*
* @param boolean $result if the process has succeeded
* @param array $args to modify the output.
*/
if( ! function_exists('succeeded') ){
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
}}
/**
* Downloads a file to a local url using the Snoopy HTTP Class
*
* @param string $url the URL of the file to download
* @return mixed false on failure, string Filename on success.
*/
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
/**
* Modification of wp_generate_tagcloud() to generate a tag cloud from a list of terms.
*
* @param array $tags list of tags and their weighting
* @param args $args the arguemetns of the function
*/
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

/**
* Determines the difference between 2 folders, Relies on wp_filesystem
*
* @param string $folder1 the source folder
* @param string $folder2 the folder to compare against.
* @return mixed, false on failure, Array of files with changes on success.
*/
if( ! function_exists('folder_diff') ){
function folder_diff($folder1, $folder2){
	global $wp_filesystem;
	if( ! $wp_filesystem || ! is_object($wp_filesystem) )
		return false;
	//Ok.
	
	$Files = array();
		
	$folder1Listing = $wp_filesystem->dirlist($folder1,false,false);
	$folder2Listing = $wp_filesystem->dirlist($folder2,false,false);

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
	foreach((array)$folder2Listing as $fileName => $fileItem){
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
}}//end function

?>