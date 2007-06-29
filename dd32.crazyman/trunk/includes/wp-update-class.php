<?php
/**
 * WP-Update Main class, This is where most of the magic is contained.
 * @author Dion Hulse
 * @version 0.1
 */
class WP_Update{
	function WP_Update(){
		include_once( ABSPATH . 'wp-includes/class-snoopy.php' );
	}
	/**
	 * Searches for a Plugin/Theme based upon tags/terms
	 * @param string $item Search type, "themes"||"plugins"
	 * @return mixed Array holding results on success, false on failure
	 */
	function search($item='themes',$tags=array(),$page=1){
		if('themes' == $item){
			if(0 == count($tags))
				return false; //Must specify search terms.
			return $this->searchThemes($tags,$page);
		} elseif('plugins' == $item){
			
		}
	}
	/*** THEME SEARCH FUNCTIONS ****/
	/** FEATURED THEEMS */
	/**
	 * Returns the themes currently featured on WordPress.org
	 * @return mixed Array holding results on success, null on failure
	 */
	function getThemesFeatured(){
		$themes = wp_cache_get('wpupdate_ThemesFeatured', 'wpupdate');
		if( !$themes ){
			$snoopy = new Snoopy();
			$snoopy->fetch('http://wordpress.org/extend/themes/themes.php');
			$themes = $this->parseThemesFeaturedHTML($snoopy->results);
			wp_cache_set('wpupdate_ThemesFeatured', $themes, 'wpupdate', 86400); //24*60*60=86400
		}
		return $themes;
	}
	/**
	 * Parses the Featured themes page on wordpress.org
	 * @param string $html the HTML of the theme page
	 * @return array of parsed page
	 */
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
	/**
	 * Searches for a theme
	 * @param array $tags tags/terms to search for
	 * @param int $page the pagenumber we're displaying
	 * @param int $items the number of items to have displayed
	 * @return mixed Array holding results on success
 	 * @todo remove the iterative loading of themes until the number is found
	 */
	function searchThemes($tags,$page=1,$items=25){
		
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
			if( count($themes) < $items ){
				$themes2 = $this->searchThemes($tags,++$page, ($items - count($themes)) );
				if( $themes2 )
					$themes = array_merge($themes, $themes2 );
			}
			
		}
		return $themes;
	}
	/**
	 * Parses a theme page from themes.wordpress.net/
	 * @param string $html the HTML of the theme page
	 * @return array of parsed page
	 */
	function parseThemeHTML($html){
		preg_match_all('#<a href="(.*?)" rel="bookmark" title="(.*?)"><img src="/snapshots/(\d+?)-thumb.jpg" alt="\2" /></a>#i',$html,$mat1);
		preg_match_all('#Download \((\d+)\)#i',$html,$mat2);
		$ret = array();
		
		$totalItems = count($mat2[1]);
		
		if( false == $mat1 )
			return false;
		
		for($i = 0; $i < $totalItems; $i++){
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
	/**
	 * Retrieves the current tag list from wordpress.org
	 * @return array of tags
	 */
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
	/**
	 * Returns plugins from WordPress.org of the specified tag
	 * @param mixed $tag the tags for the plugins wanted
	 * @param int $page the pagenumber to display
	 * @return mixed set of plugins
	 */
	function getPluginsByTag($tag=false,$page=1){
		if(!$tag) return;
		
	}
	/**
	 * Parses the Plugin page for the Search Tags
	 * @param string $html the HTML of the plugin page
	 * @return array tags including the url, number, name and pointsize
	 */
	function parseTagHTML($html){
		$ret = array();
		preg_match_all("#<a href='(.*)' title='(\d+) topics' rel='tag' style='font-size: ([\d\.]+)pt;'>(.*)</a>#i",$html,$tags);
				
		for( $i = 0; $i < count($tags[0]); $i++){
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
	/**
	 * Searches WordPress.org/extend/plugins/ for a term
	 * @param string $term the search term
	 * @return array of the search results
	 */
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
	/**
	 * Determines the Text to display for a plugin Update
	 * @param string $pluginfile the name of the plugin file
	 * @param bool $return to return the value or echo it
	 * @param bool $skipcache If the cache'd values should be ignored
	 * @param bool $forcecheck To Check for the update NOW, or to leave it
	 * @return string update Text
	 */
	function getPluginUpdateText($pluginfile=false,$return=true,$skipcache=false,$forcecheck=false){
		$updateStat = $this->checkPluginUpdate($pluginfile,$skipcache,$forcecheck);
		
		if( isset($updateStat['Errors']) ){
			//An error Occured, What is it.
			if( in_array('Not Cached',$updateStat['Errors']) ){
				if( $return ) {
					return false;
				} else {
					_e('Not Cached');
					return;
				}
			} elseif ( in_array('Not Found',$updateStat['Errors']) ){
				if( $return ) {
					return implode('<br />',$updateStat['Errors']);
				} else {
					foreach( $updateStat['Errors'] as $error){
						echo __($error);
						echo '<br />';
					}
					return;
				}
			}
		}
		if( isset($updateStat['Update']) &&
			false === $updateStat['Update'] ){
			$updateText = __('Latest Installed');
		} else {
			//Else, Theres an update available:
			$updateText = __('Update Available').':<br/>';
			$updateText .= '<strong>' . $updateStat['Version'] . '</strong>';
			if( get_option('update_install_enable') )
				$updateText .= '<br/><a href="plugins.php?page=wp-update/wp-update-plugins-install.php&url='.urlencode($updateStat['PluginInfo']['Download']).'">'.__('Install').'</a>';
			if( isset($updateStat['Errors']) ){
				$updateText .= '<br />' . implode('<br />',$updateStat['Errors']);
			}
		}
		
		if( $return )
			return $updateText;
		else
			echo $updateText;
		
	}
	/**
	 * Searches the Plugin repositories for a given plugin
	 * @param string $pluginfile the name of the plugin file
	 * @param bool $skipcache If the cache'd values should be ignored
	 * @param bool $forcecheck To Check for the update NOW, or to leave it
	 * @return array the Plugin update information on success, array of errors on failure
	 */
	function checkPluginUpdate($pluginfile=false,$skipcache=false,$forcecheck=false){
		global $wp_version;
		// Does the file exist
		if( ! $pluginfile ) return array('Errors'=>array('Invalid File'));
		
		$pluginUpdateInfo = false;
		//If cached requests are allowed, retrieve it
		if( ! $skipcache ) $pluginUpdateInfo = wp_cache_get('wpupdate_'.$pluginfile, 'wpupdate');
		//If no data is available, And we're not forcing a check, return an error
		if( ! $pluginUpdateInfo && ! $forcecheck ) return array('Errors'=>array('Not Cached'));
		
		//Get the fields from the plugin file.
		$pluginData = wpupdate_get_plugin_data(ABSPATH . PLUGINDIR . '/' . $pluginfile);
		
		//If no Update info, or we're forcing a recheck
		if( ! $pluginUpdateInfo || $forcecheck ){
			if ( '' != $pluginData['Update'] && get_option('update_location_custom') ){
				//We have a custom update URL.
				$pluginUpdateInfo = $this->checkPluginUpdateCustom($pluginData['Update']);
			}
			//Else, We check wordpress.org..  (not } else { as the custom update url may fail)
			if( !$pluginUpdateInfo && get_option('update_location_wordpressorg') ){
				//Find the plugin:
				$plugins = $this->searchPlugins($pluginData['Name']);
				if( isset($plugins['titlematch']) || isset($plugins['relevant']) ){
					$plugins['plugins'] = array_merge((array)$plugins['titlematch'],(array)$plugins['relevant']);
					foreach( (array)$plugins['plugins'] as $result){
						if( 0 === strcasecmp($result['Name'],$pluginData['Name']) ){
							//return information:
							$pluginUpdateInfo = $this->checkPluginUpdateWordpressOrg($result['Uri']);
						}
					}
				}
			}//end get_option('update_location_wordpressorg')
			
			//Update cache:
			//If Expire is not set, or Expire is not valid
			if( !empty($pluginUpdateInfo) && !isset($pluginUpdateInfo['Expire']) && ! (int) $pluginUpdateInfo['Expire'] > 0) $pluginUpdateInfo['Expire'] = 7*24*60*60;
			//If no update info is available, we cant find it.
			if( empty($pluginUpdateInfo) )
				$pluginUpdateInfo = array('Errors'=>array('Not Found'), 'Expire' =>7*24*60*60); //,'(Will check again in 1 week)'
			
			wp_cache_set('wpupdate_'.$pluginfile, $pluginUpdateInfo, 'wpupdate', $pluginUpdateInfo['Expire']);
		}
		
		//If Erorrs are set, it means we hit a snag in the update check process which has prevented checking.
		if( isset($pluginUpdateInfo['Errors']) )
			return array( 'Errors' => $pluginUpdateInfo['Errors']);
		
		//If no Plugin data available, Or the Plugin version is not specified, we cant do anything for the plugin.
		if( !$pluginUpdateInfo || !$pluginUpdateInfo['Version'] )
			return array('Errors'=>array('Not Compatible','(No Version specified on update page)'));

		if( version_compare($pluginUpdateInfo['Version'] , $pluginData['Version'], '>') ){
			//Theres a new version available!, Now, Check its Requirements.
			$pluginCompatible = true; //We'll override this later
			$errors = array();
			foreach((array)$pluginUpdateInfo['Requirements'] as $reqInfo){
				//$reqInfo = array( 'Name', 'Type', 'Min', 'Tested');
				//If the Requirement Name is not set, Set it to the Type.
				if( !isset($reqInfo['Name']) || empty($reqInfo['Name']) )
					$reqInfo['Name'] = $reqInfo['Type'];

				switch($reqInfo['Type']){
					case "WordPress":
						//Check the minimum version needed
						if( isset($reqInfo['Min']) ){
							if( ! version_compare( $wp_version, $reqInfo['Min'], '>=' ) ){
								$pluginCompatible = false;
								$errors[] = sprintf('Requires %s %s',$reqInfo['Name'],$reqInfo['Min']);
							}
						}
						//Check the Maximum version that its been tested with
						if( isset($reqInfo['Tested']) ){
							if( version_compare( $wp_version, $reqInfo['Tested'], '>' ) ){
								$errors[] = sprintf('Only tested Upto %s %s',$reqInfo['Name'],$reqInfo['Tested']);
							}
						}
						break;
					case "PHP":
						if( isset($reqInfo['Min']) ){
							if( ! version_compare( phpversion(), $reqInfo['Min'], '>=' ) ){
								$pluginCompatible = false;
								$errors[] = sprintf('Requires %s %s',$reqInfo['Name'],$reqInfo['Min']);
							}
						}
						if( isset($reqInfo['Tested']) ){
							if( version_compare( phpversion(), $reqInfo['Tested'], '>' ) ){
								$errors[] = sprintf('Only tested Upto %s %s',$reqInfo['Name'],$reqInfo['Tested']);
							}
						}
						break;
					case "MySQL":
						if( isset($reqInfo['Min']) ){
							if( ! version_compare( mysql_get_server_info(), $reqInfo['Min'], '>=' ) ){
								$pluginCompatible = false;
								$errors[] = sprintf('Requires %s %s',$reqInfo['Name'],$reqInfo['Min']);
							}
						}
						if( isset($reqInfo['Tested']) ){
							if( version_compare( mysql_get_server_info(), $reqInfo['Tested'], '>' ) ){
								$errors[] = sprintf('Only testd Upto %s %s',$reqInfo['Name'],$reqInfo['Tested']);
							}
						}
						break;
					case "Plugins":
						//TODO
						break;
					case "PHPExt":
						if( ! extension_loaded( strtolower($reqInfo['Name']) ) ){
							$errors[] = sprintf('Requires the PHP Extension: "%s"',$reqInfo['Name']);
						} else {
							//Extension loaded, Check version???
							$functs = get_extension_funcs( strtolower($reqInfo['Name']) );
							//Iterate through library functions looking for something for version
							foreach($functs as $function){
								//If we've found one with version..
								if( strpos($function,'version') > -1){
									$version = @call_user_func($function);
									if( isset($reqInfo['Min']) ){
										if( ! version_compare( $version, $reqInfo['Min'], '>=' ) ){
											$pluginCompatible = false;
											$errors[] = sprintf('Requires %s %s',$reqInfo['Name'],$reqInfo['Min']);
										}
									}
									if( isset($reqInfo['Tested']) ){
										if( version_compare( $version, $reqInfo['Tested'], '>' ) ){
											$errors[] = sprintf('Only testd Upto %s %s',$reqInfo['Name'],$reqInfo['Tested']);
										}
									}//ISSET
									break;
								}//strpos
							}//foreach;
						}
						break;
					default:
				} //end switch()
			} //end foreach()
			
			$updateReturn = array(
								'Update'=>true,
								'Compatible'=>$pluginCompatible,
								'Version'=>$pluginUpdateInfo['Version'], 
								'PluginInfo' => $pluginUpdateInfo
								);
			//If any errors occured, Add it in:
			if( !empty($errors) )
				$updateReturn = array_merge($updateReturn, array('Errors'=>$errors));
			return $updateReturn;
		} else {
			//The currently installed version is the latest availaable.
			return array('Update'=>false);
		}
	}
	/**
	 * Parses the WordPress.org plugin page for an individual plugins details
	 * @param string $uri the URL of the Plugin page to parse
	 * @return mixed array of Plugin details
	 */
	function checkPluginUpdateWordpressOrg($uri){
		if ( ! $uri ) return false;
		
		$snoopy = new Snoopy();
		$snoopy->fetch($uri);
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
			$wordpress = array('Name' => 'WordPress',
							   'Type' => 'Wordpress');
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
					'Download'	=>	trim($download[1]),
					'Author'	=>	trim($authordetails[2]),
					/* 'WPAuthor'	=>	trim($authordetails[1]),*/
					'AuthorHome'=>	trim($authorhomepage[1]),
					'PluginHome'=>	trim($pluginhomepage[1]),
					'Rating'	=>	trim($rating[1]),
					'Tags'		=>	$final_tags,
					'Related'	=>	$final_related,
					'Requirements' => $requirements,
					'Expire'	=> 7*24*60*60
					);
	}
	
	/**
	 * Checks a Custom update URL for a plugin
	 * @param string $uri the update link for the plugin
	 * @return mixed array of details on sucess, false on failure
	 */
	function checkPluginUpdateCustom($uri){
		$snoopy = new Snoopy();
		$snoopy->fetch($uri);
		//TODO: If Is serialised data, then return, else return null.. 
		//		Also should determine the type of the data, and if its a URL of wordpress.org or something
		if( is_serialized($snoopy->results) ){
			$data = unserialize($snoopy->results);
			if( isset($data['Errors']) && in_array('Unknown Plugin',$data['Errors']) )
				$data = false;
		/*} elseif( is_rss($snoopy->results){
			Blah */
		} else {
			$data = false;
		}
		return $data;		
	}
	/** INSTALL FUNCITONS **/
	/**
	 * Installs a theme from a given URL
	 * @param string $url the URL of the theme to install
	 * @return void
	 */
	function installThemeFromURL($url){
		$snoopy = new Snoopy();
		$snoopy->fetch($url);
		
		$tmpfname = tempnam("/tmp", "theme");

		$handle = fopen($tmpfname, "w");
		fwrite($handle, $snoopy->results);
		fclose($handle);

		$this->installTheme($tmpfname, array('name'=>basename($url)));
		
		unlink($tmpfname);
	}
	/**
	 * Installs a theme from a local File
	 * @param string $file the Location of the local file
	 * @param mixed $fileinfo Info given about the file, Similar to $_FILES
	 * @return void
	 */
	function installTheme($file,$fileinfo=array()){
		require_once('pclzip.lib.php');
		if( !empty($fileinfo) && ! strpos($fileinfo['type'],'zip') > 0 ){
			//Invalid File given.
			$step = 1;
			echo '<strong>Invalid Archive selected</strong><br/>';
		} else {
			//potentially Valid.
			$archive = new PclZip($file);
			if( false === ($archiveFiles = $archive->listContent()) ){
				$step = 1;
				echo '<strong>Invalid Archive selected<br/>'.$archive->errorInfo(true).'</strong><br/>';
			} else {
				//Seems its OK!
				echo '<strong>Valid Archive selected</strong><br/>';
				$this->installThemeStep2($archive,$fileinfo);
			}
		}
	}
	/**
	 * Installs a theme from a open Archive
	 * @param mixed $archive the PCLZip Archive Object of the archive
	 * @param mixed $fileinfo Info given about the file, Similar to $_FILES
	 * @return false on failure, null otherwise
	 * @todo move the OK/FAIL messages to own function
	 */
	function installThemeStep2($archive,$fileinfo=array()){
		if( ! $archive) return false;
		if( false === ($files = $archive->listContent()) ) return false;

		/* Filesystem */
		require_once('wp-update-filesystem-class.php');
		$fs = WP_Filesystem();
		
		//First of all, Does the zip file contain a base folder?
		$base = $fs->get_base_dir() . 'wp-content/themes/';
		$baseFolderName = false;
		foreach((array)$files as $thisFileInfo){
			//If no Slash then it needs to be put in a folder
			if( false === strpos($thisFileInfo['filename'],'/') ){
				$baseFolderName = true;
				break;
			}
		}
		
		if( $baseFolderName ){
			//The theme file files's are not contained within a single folder, So we need to put them into a subfolder:
			echo __('<Strong>Installing</strong>: ').$base.'<br/>';
			
			echo __('<strong>Creating folder</strong>: ') . basename($fileinfo['name'],'.zip');
			if( $fs->mkdir( $base . basename($fileinfo['name'],'.zip') ) )
				echo ' <span style="color: green;">['.__('OK').']</span><br>';
			else
				echo ' <span style="color: red;">['.__('FAILED').']</span><br>';
				
			$base .= basename($fileinfo['name'],'.zip') . '/';				
		} else {
			//All files are within a folder inside the archive:
			$tmppath = '';
			$path = explode('/',$files[0]['filename']);
			echo __('<Strong>Installing to</strong>: ').$base . $path[0].'<br/>';
			
			//Loop through the folder list and create any needed folders
			for( $j = 0; $j < count($path) - 1; $j++ ){
				$tmppath .= $path[$j] . '/';
				if( ! $fs->is_dir($base . $tmppath) ){
					echo __('<strong>Creating folder</strong>: ') . $tmppath;
					if( $fs->mkdir($base . $tmppath) )
						echo ' <span style="color: green;">['.__('OK').']</span><br>';
					else
						echo ' <span style="color: red;">['.__('FAILED').']</span><br>';
				}//end if
			}//end for
		}//end if($baseFolderName

		//Extract each file into the array
		$files = $archive->extract(PCLZIP_OPT_EXTRACT_AS_STRING);
		
		for( $i=1; $i<count($files); $i++){
			//If a folder, Create
	  		if( $files[$i]['folder'] ){
				echo __('<strong>Creating folder</strong>: ') . $files[$i]['filename'];
				if( $fs->mkdir($base.$files[$i]['filename']) )
					echo ' <span style="color: green;">['.__('OK').']</span><br>';
				else
					echo ' <span style="color: red;">['.__('FAILED').']</span><br>';
			} else {
				//File here, We need to make sure all the folders it needs are created allready
				$tmppath = '';
				$path = explode('/',$files[$i]['filename']);
				for( $j = 0; $j < count($path) - 1; $j++ ){
					$tmppath .= $path[$j] . '/';
					if( ! $fs->is_dir($base . $tmppath) ){
						echo __('<strong>Creating folder</strong>: ') . $base . $tmppath;
						if( $fs->mkdir($base . $tmppath) )
							echo ' <span style="color: green;">['.__('OK').']</span><br>';
						else
							echo ' <span style="color: red;">['.__('FAILED').']</span><br>';
					}
				}
				//Inflate the file now.
				echo __('<strong>Inflating File</strong>: ') . $files[$i]['filename'];
		  		if( $fs->put_contents($base.$files[$i]['filename'], $files[$i]['content']) )
					echo ' <span style="color: green;">['.__('OK').']</span><br>';
				else
					echo ' <span style="color: red;">['.__('FAILED').']</span><br>';
			}// end if(folder)
		}//end for
		return true;
	}//end function installThemeStep2
}
?>
