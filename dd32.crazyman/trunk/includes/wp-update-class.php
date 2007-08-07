<?php
/**
 * WP-Update Main class, This is where most of the magic is contained.
 * @author Dion Hulse
 * @version 0.1
 */
class WP_Update{
	function WP_Update(){
		require_once( ABSPATH . 'wp-includes/class-snoopy.php' );
		require_once('wp-update-functions.php');
		$this->loadPlugins();
	}
	/**
	 * Loads any wp-update Extensions which are set.
	 *
	 * @return null
	 */	
	function loadPlugins(){
		if( ! is_dir(ABSPATH . 'wp-content/plugins/wp-update/extensions') )
			return;
		$dir = @dir(ABSPATH . 'wp-content/plugins/wp-update/extensions');
		while (false !== ($file = $dir->read()))
			@ include ( $dir->path . '/' . $file );
	}
	/**
	 * Searches for a Plugin/Theme based upon tags/terms
	 * @param string $item Search type, "themes"||"plugins"
	 * @return mixed Array holding results on success, false on failure
	 */
	function search($item='themes',$terms=array(),$page=1){

		if( empty($page) || ! is_numeric($page) )
			$page = 1;
		if( ! is_array($terms) )
			$tags = array($terms);

		if('themes' == $item){
			return apply_filters('wpupdate_themeSearchProviders',
					array('results'=>array(),
							'info'=>array(
								'page'=>$page,
								'pages'=>0,
								'searchOptions'=>$terms['searchOptions'],
								'sortby'=>$terms['sortby'],
								'order'=>$terms['order'],
								'andor'=>$terms['andor']
								)
							));	
		} else {
			return apply_filters('wpupdate_pluginSearchProviders',
					array('results'=>array(),
							'info'=>array(
								'terms'=>$terms,
								'page'=>$page)
							));
		}
	}
	/*** THEME SEARCH FUNCTIONS ****/
	/** FEATURED THEEMS */
	/**
	 * Returns the themes currently featured on WordPress.org
	 * @return mixed Array holding results on success, null on failure
	 */
	function getThemesFeatured(){
		return apply_filters('wpupdate_themesFeatured',array('results'=>array(),'info'=>array()));
	}
	/*** PLUGIN FUNCTIONS ***/
	/** PLUGIN TAG FUNCTIONS **/
	/**
	 * Retrieves the current tag list from the Tag Providers
	 * @return array of tags
	 */
	function getPluginSearchTags(){
		return apply_filters('wpupdate_pluginTagList',array());
	}
	/**
	 * Returns plugins from WordPress.org of the specified tag
	 * @param mixed $tag the tags for the plugins wanted
	 * @param int $page the pagenumber to display
	 * @return mixed set of plugins
	 */
	function getPluginsByTag($tag=false,$page=1){
		$results = array('results'=>array(),
						'info'=>array('terms'=>$tag,
									  'page'=>$page,
									  'pages'=>1)//Set a default number of pages, We'll override this later
					); 
		return apply_filters('wpupdate_pluginTagSearch',$results);
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
			$this->updateNotifications(); //update notifications.
			//Else, Theres an update available:
			$updateText = __('Update Available').':<br/>';
			$updateText .= '<strong>' . $updateStat['Version'] . '</strong>';
			if( get_option('update_install_enable') )
				$updateText .= '<br/><a href="plugins.php?page=wp-update/wp-update-plugins-install.php&url='.urlencode($updateStat['PluginInfo']['Download']).'&upgrade='.$pluginfile.'">'.__('Install').'</a>';
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

		// Does the file exist
		if( ! $pluginfile ) return array('Errors'=>array('Invalid File'));
		
		$pluginUpdateInfo = false;
		//If cached requests are allowed, retrieve it
		if( ! $skipcache ) 
			$pluginUpdateInfo = wp_cache_get('wpupdate_'.$pluginfile, 'wpupdate');
		//If no data is available, And we're not forcing a check, return an error
		if( ! $pluginUpdateInfo && ! $forcecheck )
			return array('Errors'=>array('Not Cached'));
		
		//Get the fields from the plugin file.
		$pluginData = wpupdate_get_plugin_data(ABSPATH . PLUGINDIR . '/' . $pluginfile);
		
		//If no Update info, or we're forcing a recheck
		if( ! $pluginUpdateInfo || $forcecheck ){
			$pluginData['Update'] = apply_filters('wpupdate_update-url-' . $pluginfile, $pluginData['Update']);
			if ( !empty($pluginData['Update']) ){
				//We have a custom update URL.
				$pluginUpdateInfo = $this->checkPluginUpdateCustom($pluginData['Update']);
			}
			//Else, We check the plugin searches  (not } else { as the custom update url may fail)
			if( !$pluginUpdateInfo && get_option('update_location_search') ){
				//Find the plugin:
				$plugins = $this->search('plugins',$pluginData['Name']);
				if( ! empty($plugins) ){
					foreach( (array)$plugins['results'] as $result){
						if( 0 === strcasecmp($result['Name'],$pluginData['Name']) ){
							//return information:
							$pluginUpdateInfo = $this->checkPluginUpdateURL($result['UpdateURL']);
							break;
						}
					}
				}
			}//end get_option('update_location_search')
			
			//Update cache:
			//If Expire is not set, or Expire is not valid
			if( !empty($pluginUpdateInfo) && ( !isset($pluginUpdateInfo['Expire']) || ! is_numeric($pluginUpdateInfo['Expire']) ) )
				$pluginUpdateInfo['Expire'] = 7*24*60*60;
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
			return array( 'Errors' =>array('Not Compatible','(No Version specified on update page)'));

		$pluginUpdateInfo = apply_filters('wpupdate_plugin-updateinfo-' . $pluginUpdateInfo, $pluginUpdateInfo);

		if( version_compare($pluginUpdateInfo['Version'] , $pluginData['Version'], '>') ){
			//Theres a new version available!, Now, Check its Requirements.
			return array_merge($this->checkPluginCompatible($pluginUpdateInfo), 
								array(
									'Update'	=>true,
									'Version'	=>$pluginUpdateInfo['Version'], 
									'PluginInfo'=> $pluginUpdateInfo
									) );
		} else {
			//The currently installed version is the latest availaable.
			return array('Update'=>false);
		}
	}
	
	
	function checkPluginUpdateURL($url){
		$update = null;
		//First, Get the Hostname
		$components = parse_url($url);
		//Second, Run it through a filter to see if any extension picks it up as a known handle
		$update = apply_filters('wpupdate_checkPluginUpdate-' . strtolower($components['host']), $url);
		//Third, If no plugin picked it up, Query the URL directly.
		if ( ! $update )
			$update = $this->checkPluginUpdateCustom($url);
		return $update;
	}
	
	function checkPluginCompatible($pluginUpdateInfo){
		global $wp_version;
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
					if( isset($reqInfo['Min']) && !empty($reqInfo['Min']) ){
						if( ! version_compare( $wp_version, $reqInfo['Min'], '>=' ) ){
							$pluginCompatible = false;
							$errors[] = sprintf(__('Requires %s %s'),$reqInfo['Name'],$reqInfo['Min']);
						}
					}
					//Check the Maximum version that its been tested with
					if( isset($reqInfo['Tested']) && !empty($reqInfo['Tested']) ){
						if( version_compare( $wp_version, $reqInfo['Tested'], '>' ) ){
							$errors[] = sprintf(__('Only tested Upto %s %s'),$reqInfo['Name'],$reqInfo['Tested']);
						}
					}
					break;
				case "PHP":
					if( isset($reqInfo['Min']) && !empty($reqInfo['Min']) ){
						if( ! version_compare( phpversion(), $reqInfo['Min'], '>=' ) ){
							$pluginCompatible = false;
							$errors[] = sprintf(__('Requires %s %s'),$reqInfo['Name'],$reqInfo['Min']);
						}
					}
					if( isset($reqInfo['Tested']) && !empty($reqInfo['Tested']) ){
						if( version_compare( phpversion(), $reqInfo['Tested'], '>' ) ){
							$errors[] = sprintf(__('Only tested Upto %s %s'),$reqInfo['Name'],$reqInfo['Tested']);
						}
					}
					break;
				case "MySQL":
					if( isset($reqInfo['Min']) && !empty($reqInfo['Min']) ){
						if( ! version_compare( mysql_get_server_info(), $reqInfo['Min'], '>=' ) ){
							$pluginCompatible = false;
							$errors[] = sprintf(__('Requires %s %s'),$reqInfo['Name'],$reqInfo['Min']);
						}
					}
					if( isset($reqInfo['Tested']) && !empty($reqInfo['Tested']) ){
						if( version_compare( mysql_get_server_info(), $reqInfo['Tested'], '>' ) ){
							$errors[] = sprintf(__('Only tested Upto %s %s'),$reqInfo['Name'],$reqInfo['Tested']);
						}
					}
					break;
				case "Plugin":
					$foundPlugin = false;
					$plugins = wpupdate_get_plugins();
					foreach($plugins as $plugin){
						if( false !== strcasecmp($plugin['Name'], $reqInfo['Name']) ){
							if( isset($reqInfo['Min']) ){
								if( ! version_compare( $plugin['Version'], $reqInfo['Min'], '>=' ) ){
									$pluginCompatible = false;
									$errors[] = sprintf(__('Requires WordPress Plugin %s %s'),$reqInfo['Name'],$reqInfo['Min']);
								}
							}
							if( isset($reqInfo['Tested']) ){
								if( version_compare( $plugin['Version'], $reqInfo['Tested'], '>' ) ){
									$errors[] = sprintf(__('Only tested with version %s of the plugin %s'),$reqInfo['Tested'], $reqInfo['Name']);
								}
							}
							$foundPlugin = true;
							break;
						}
					}
					if( ! $foundPlugin )
						$errors[] = sprintf(__('Requires the WordPress Plugin: "%s"'),$reqInfo['Name']);
					break;
				case "PHPExt":
					if( ! extension_loaded( strtolower($reqInfo['Name']) ) ){
						$errors[] = sprintf(__('Requires the PHP Extension: "%s"'),$reqInfo['Name']);
					} else {
						if( isset($reqInfo['Min']) ){
							if( ! version_compare( phpversion($reqInfo['Name']), $reqInfo['Min'], '>=' ) ){
								$pluginCompatible = false;
								$errors[] = sprintf(__('Requires %s %s'),$reqInfo['Name'],$reqInfo['Min']);
							}
						}
						if( isset($reqInfo['Tested']) ){
							if( version_compare( phpversion($reqInfo['Name']), $reqInfo['Tested'], '>' ) ){
								$errors[] = sprintf(__('Only tested Upto %s %s'),$reqInfo['Name'],$reqInfo['Tested']);
							}
						}
					}
					break;
				default:
					/* array(&..) because PHP5 requires pass-by-reference to be specifically stated in function declaration; 
						when using func_get_args() as do_action does, everything is passed by value, 
						allthough objects passed by reference, and references in arrays are kept intact. 
						This allows for Errors and Compatibility to be passed back to the function */
					do_action('wpupdate_requirement-'.$reqInfo['Type'], array(&$pluginUpdateInfo, &$reqInfo, &$pluginCompatible,&$errors));
			} //end switch()
		} //end foreach()

		$pluginCompatible = array('Compatible'=>$pluginCompatible);
		if( !empty($errors) )
			$pluginCompatible['Errors'] = $errors;
			
		return $pluginCompatible;
	}
	
	/**
	 * Checks a Custom update URL for a plugin
	 * @param string $uri the update link for the plugin
	 * @return mixed array of details on sucess, false on failure
	 */
	function checkPluginUpdateCustom($uri){
		$snoopy = new Snoopy();
		$snoopy->fetch($uri);
		//TODO: Also should determine the type of the data, and if its a URL of wordpress.org or something
		if( strpos($snoopy->results, '<?xml') > -1 ){
			$data = $this->__PluginUpdateCustomParse($snoopy->results);
		/*} elseif( is_rss($snoopy->results){
			Blah */
		} else {
			$data = false;
		}
		return $data;		
	}
	function  __PluginUpdateCustomParse($data){
		preg_match('#<plugin>(.*?)<\/plugin>#is',$data,$items);
			preg_match('#<name>(.*?)<\/name>#i'				,$items[1],$pluginname);
			preg_match('#<version>(.*?)<\/version>#i'		,$items[1],$version);
			preg_match('#<lastupdate>(.*?)<\/lastupdate>#i'	,$items[1],$lastupdate);
			preg_match('#<download>(.*?)<\/download>#i'		,$items[1],$download);
			preg_match('#<author>(.*?)<\/author>#i'			,$items[1],$author);
			preg_match('#<authorhomepage>(.*?)<\/authorhomepage>#i'	,$items[1],$authorhome);
			preg_match('#<pluginhomepage>(.*?)<\/pluginhomepage>#i'	,$items[1],$pluginhome);
			preg_match('#<expire>(\d+?)<\/expire>#i'			,$items[1],$expire);

			preg_match('#<requirements>(.*?)<\/requirements>#is',$items[1],$_requirements);
				preg_match_all('#<requirement>(.*?)<\/requirement>#is',$_requirements[1],$_requirements);
					for($i=0; $i < count($_requirements[1]);$i++){
						preg_match('#<name>(.*?)<\/name>#i',$_requirements[1][$i],$name);
						preg_match('#<type>(.*?)<\/type>#i',$_requirements[1][$i],$type);
						preg_match('#<minversion>(.*?)<\/minversion>#i',$_requirements[1][$i],$min);
						preg_match('#<tested>(.*?)<\/tested>#i',$_requirements[1][$i],$tested);
						$requirements[] = array('Name'=>$name[1], 'Type'=>$type[1], 'Min'=>$min[1], 'Tested'=>$tested[1]);
					}
		return array(
						'Name' => $pluginname[1],
						'Version' => $version[1],
						'LastUpdate' => $lastupdate[1],
						'Download' => $download[1],
						'Author' => $author[1],
						'AuthorHomepage' => $authorhome[1],
						'PluginHomepage' => $pluginhome[1],
						'Expire' => $expire[1],
						'Requirements' => $requirements
					);
	}
	
	function updateNotifications(){
		$previous = get_option('wpupdate_notifications');
		$new = array();
		//Check for the current updates.
		$plugins = wpupdate_get_plugins();
		foreach((array)$plugins as $plugin_file => $plugin_info){
			$plugin = $this->checkPluginUpdate($plugin_file);
			if( isset($plugin['Update']) && true == $plugin['Update'] )
				$new[ $plugin_file ] = $plugin;
		}
		//Next, Clear any updates which have been installed
		foreach((array)$previous as $plugin_file => $plugin_info){
			if( ! isset($new[ $plugin_file ]) ){
				unset ($previous[ $plugin_file ]);
			} else {
				//Item exists.
				if( $previous[ $plugin_file ]['HideUpdate'] && 
						$previous[ $plugin_file ]['Version'] == $new[ $plugin_file ]['Version'])//Only honour it if the versions are the same
					$new[ $plugin_file ]['HideUpdate'] = true;
				else
					$new[ $plugin_file ]['HideUpdate'] = false;
			}
		}
		update_option('wpupdate_notifications',$new);
		//Now $previous contains the updates that were available last time, $new contains those which are available this time.

		if( $new !== $previous ){
			$enabled = get_option('update_email_enable');
			$email = get_option('update_email_email');
			if( $enabled && !empty($email) ){
				$updatedPlugins = array();
				foreach($new as $plugin_file => $plugin_info){
					if( true == $plugin_info['HideUpdate'] )
						continue;
					$updatedPlugins[] = $plugin_info['PluginInfo']['Name'] . ' ' . $plugin_info['PluginInfo']['Version'] . "\n";
				}
				if( $pluginsCount > 0){
					$message = sprintf(__('You have %d update(s) available.'),count($updatedPlugins));
					$message .= "\n\n";
					$message .= implode("\n",$updatedPlugins);
					$message .= "\n\n";
					$message .= __('Wordpress Admin page:') . ' ' . get_bloginfo('wpurl') . '/wp-admin/plugins.php';
					
					$subject = get_bloginfo('name') . ': New Updates available for install';
					wp_mail( $email, $subject, $message);
				}//End if plugins
			}//end if enabled
		}//end if changes.
	}//end function updateNotifications()
	
	/** INSTALL FUNCITONS **/
	function installPlugin($filename,$fileinfo=array()){
		return $this->installItem($filename, $fileinfo, 'wp-content/plugins/');
	}
	function installTheme($filename,$fileinfo=array()){
		return $this->installItem($filename, $fileinfo, 'wp-content/themes/');
	}
	function installItem($filename,$fileinfo=array(),$destination=''){
		require_once('wp-update-filesystem-class.php');
		require_once('pclzip.lib.php');	
		$messages = array();
		
		if( ! $filename )
			return false;
		/* NOTE: If this uses too much memory, it might be possible to just extract each file as needed rather than extracting entire archive into memory */
		$archive = new PclZip($filename);

		//Check to see if its a Valid archive
		if( false == ($archiveFiles = $archive->extract(PCLZIP_OPT_EXTRACT_AS_STRING)) ){
			return array('Errors'=>array('Incompatible Archive'));
		} else {
			$messages[] = 'Valid Archive Selected';
		}
		if ( 0 == count($archiveFiles) )
			return array('Errors'=>array('Empty Archive'));
		
		$fs = WP_Filesystem();

		if( ! is_object($fs) )
			return array('Errors'=>array('Filesystem options not set correctly'));
		
		//First of all, Does the zip file contain a base folder?
		$base = $fs->get_base_dir() . $destination;
		$messages[] = "Base Directory: <strong>$base</strong>";
		
		$fs->setDefaultPermissions( $fs->getchmod($base) );
		
		//Check if the destination directory exists, If not, create it.
		$path = explode('/',$base);
		$tmppath = '';
		for( $j = 0; $j < count($path) - 1; $j++ ){
				$tmppath .= $path[$j] . '/';
				if( ! $fs->is_dir($tmppath) )
					$messages[] = __('<strong>Creating folder</strong>: ') . $tmppath . succeeded( $fs->mkdir($tmppath) );
		}
		
		if( count($archiveFiles) > 1){
			//Multiple files, they'll need to be in a folder
			$baseFolderName = false;

			foreach((array)$archiveFiles as $thisFileInfo){
				//If no Slash then it needs to be put in a folder
				
				if( false === strpos($thisFileInfo['filename'],'/') ){
					$messages[] = 'Installing to Subdirectory: <strong>' . basename($fileinfo['name'],'.zip') . '</strong>';

					$base .= basename($fileinfo['name'],'.zip');
					if( $fs->is_dir($base) )
						return array('Errors'=>array('Folder Exists! Install cannot continue' . $base));
					
					$messages[] = __('<strong>Creating folder</strong>: ') . basename($fileinfo['name'],'.zip') . succeeded( $fs->mkdir( $base ) );
					break; //We've created any folders we need, we can now break out of this loop.
				}
			}
		}
		//Inflate the files and Create Directory Structure
		foreach($archiveFiles as $archiveFile){
			$path = explode('/',$archiveFile['filename']);
			$tmppath = '';
			//Loop through each of the items and check the folder exists.
			for( $j = 0; $j < count($path) - 1; $j++ ){
				$tmppath .= $path[$j] . '/';
				if( ! $fs->is_dir($base . $tmppath) )
					$messages[] = __('<strong>Creating folder</strong>: ') . $tmppath . succeeded( $fs->mkdir($base . $tmppath) );
			}//end for
			//We've made sure the folders are there, So lets extract the file now:
			if( ! $archiveFile['folder'] )
				$messages[] = __('<strong>Inflating File</strong>: ') . $archiveFile['filename'] . 
							succeeded( $fs->put_contents($base.$archiveFile['filename'], $archiveFile['content']) );
		}
		return $messages;
	} //end installItem()
}
?>