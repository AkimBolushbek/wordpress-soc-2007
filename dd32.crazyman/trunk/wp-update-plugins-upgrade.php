<?php
if( !defined('ABSPATH') || ! $wp_update )
	die('Cannot be called directly.');
check_admin_referer('wpupdate-upgrade-plugin');

require_once('includes/wp-update-filesystem.php');
$installedInfo = wpupdate_get_plugin_data(ABSPATH . PLUGINDIR . '/' . $_GET['upgrade']);
$action = array();
foreach($_GET as $key=>$val)
	$action[] = $key .'='. urlencode($val);
?>
<form action="<?php echo $pagenow . '?' . implode('&amp;',$action); ?>" method="post">
<?php 
wp_nonce_field('wpupdate-upgrade-plugin');

$opt = array(); //Options for the filesystem.
if( isset($_POST['required']) ){
	if( !empty($_POST['required']) ){
		foreach((array)$_POST['required'] as $key=>$value){
			echo '<input type="hidden" name="required['.$key.']" value="'.attribute_escape($value).'" />';
			$opt[$key] = $value;
		}
	}
}

global $wp_filesystem;
if( ! $wp_filesystem || ! is_object($wp_filesystem) )
	WP_Filesystem();
if( ! is_object($wp_filesystem) )
	wp_die('WP_Filesystem Error:' . print_r($wp_filesystem,true));
			
if( !empty($wp_filesystem->errors) ){ 
?>
<div class="wrap">
	<h2><?php _e('Extra Filesystem Information is required'); ?></h2>
	<?php 
		foreach((array)$wp_filesystem->errors as $id=>$values){
			if( 'require' == $id )
				continue;
			echo $value . '<br />';
		}
		if( !empty($wp_filesystem->errors['require']) ){
			foreach((array)$fs_compat['require'] as $id=>$value)
				echo '<b>' . __($value) . ':</b><input type="text" name="required['.$id.']" value="" /><br />';
		}
	?>
</div>
<?php } ?>
<div class="wrap">
<?php
	if( isset($_POST['submit_cancel']) ){
		//Delete upgrade stuff
		$folder = ABSPATH . 'wp-content/wpupdate/' . $_POST['upgrade_source'];
		$wp_filesystem->delete($folder, true);
		echo '<p>';
			_e('All files relating to the upgrade proceedure have been deleted.');
			echo '<br>';
			_e('The folder which holds the upgrade files was:');
			echo $folder;
		echo '</p>';
	} elseif( isset($_POST['submit_proceed']) ){
		//Lets do the upgrade (And then delete the old stuff)
		$oldfile = PLUGINDIR . '/' . $_GET['upgrade']; //Full path to the plugin FILE, NOT PATH
		$source = 'wp-content/wpupdate/' . $_POST['upgrade_source'];
		$destination = PLUGINDIR . '/' . basename($source);
		
		//First Deactivate the plugin.
		$current = get_option('active_plugins');
		array_splice($current, array_search( $_GET['upgrade'], $current), 1 ); // Array-fu!
		update_option('active_plugins', $current);
		//do_action('deactivate_' . trim( $_GET['plugin'] )); //Dont do the deactivate action.
		do_action('update_' . trim( $_GET['upgrade'] )); 
		
		//Remove old files
		//First, Remove the old directory if need be:
		if( dirname($oldfile) !== dirname($destination . '/.') ){ // /. to make sure we get the foldername rather than the folder being treated as a file
			//We need to remove the old files first.
			$file = basename(str_replace(array(ABSPATH, PLUGINDIR),'',$oldfile));
			$file = preg_replace("|^/+|",'',$file);//strip leading slashes
			if( basename($file) == $file){
				//File:
				$messages[] = __('<strong>Deleteing file</strong>: ') . 
							$file . 
							succeeded( $wp_filesystem->delete( ABSPATH . PLUGINDIR . '/' . $file) );
			} else {
				$messages[] = __('<strong>Deleteing Folder</strong>: ') . 
							$file . 
							succeeded( $wp_filesystem->delete( ABSPATH . PLUGINDIR . '/' . $file, true) );
			}
		}
		$diff = folder_diff(ABSPATH . $source, ABSPATH . $destination);

		//Create folder structure
		$path = explode('/',$destination);
		$tmppath = ABSPATH . '/';
		for( $j = 0; $j < count($path); $j++ ){
			$tmppath .= $path[$j] . '/';
			if( ! $wp_filesystem->is_dir($tmppath) )
				$messages[] = __('<strong>Creating folder</strong>: ') . $tmppath . succeeded( $wp_filesystem->mkdir($tmppath) );
		}

		foreach((array) $diff as $filename=>$fileInfo){
			switch($fileInfo['status']) {
				case 'deleted':
					$messages[] = __('<strong>Deleteing file</strong>: ') . 
							$filename . 
							succeeded( $wp_filesystem->delete( ABSPATH . $destination . '/' . $filename, true) );
					break;
				case 'changed':
					$messages[] = __('<strong>Deleteing file</strong>: ') . 
							$filename . 
							succeeded( $wp_filesystem->delete( ABSPATH . $destination . '/' . $filename, true) );
					//No Break, We delete the file, then treat it as a new file.
				case 'new':
					$messages[] = __('<strong>Installing file</strong>: ') . 
							$filename . 
							succeeded( $wp_filesystem->copy( ABSPATH . $source . '/' . $filename, ABSPATH . $destination . '/' . $filename) );
					break;
				default:
				case 'same':
					//Leave unchanged files alone.
					break;
			}
		}
		echo implode("<br>",$messages);
		$folder = ABSPATH . 'wp-content/wpupdate/' . $_POST['upgrade_source'];
		$wp_filesystem->delete($folder, true);
	} else {
?>
	<h2><?php _e('Currently installed:'); ?></h2>
	<strong><?php _e('Title:'); ?></strong> <?php echo $installedInfo['Title']; ?><br/>
	<strong><?php _e('Author:'); ?></strong> <?php echo $installedInfo['Author']; ?><br/>
	<strong><?php _e('Version:'); ?></strong> <?php echo $installedInfo['Version']; ?><br/>
	<em><?php echo $installedInfo['Description']; ?></em>
	<?php if( empty($wp_filesystem->errors) ){ ?>
	<h2><?php _e('Upgrade file:'); ?></h2>
			<?php
			$file = $_GET['url'];
			$fileinfo = pathinfo($file);
			$fileinfo['name'] = $fileinfo['basename'];
			$filename = wpupdate_url_to_file($file);
			
			$messages = $wp_update->installItemFromZip($filename, $fileinfo, 'wp-content/wpupdate/');
			unlink($filename); //Once installed, Delete the zip

			echo '<div class="installLog">';
				foreach($messages as $message){
					echo $message.'<br />';
				}
			echo '</div>';
			
			$plugins = wpupdate_get_plugins(ABSPATH . 'wp-content/wpupdate/');
			if( 1 == count($plugins) ){
				foreach($plugins as $pluginFile => $plugin){ //We want the first one from the array.
					$newdata = $plugin;
					$newdata['Folder'] = dirname($pluginFile);
					break;
				}
			} else {
				foreach($plugins as $pluginFile => $plugin){ //We want the first one from the array.
					if( $plugin['Name'] != $installedInfo['Name'] )
						continue;
					$newdata = $plugin;
					$newdata['Folder'] = dirname($pluginFile);
				}
			}

			echo '<input type="hidden" name="upgrade_source" value="' . $newdata['Folder'] . '" />';

			echo '<strong>' . __('Source') . ':</strong> ' . $file . '<br />';
			echo '<strong>'.__('Title').':</strong> '.$newdata['Title'].'<br>';
			echo '<strong>'.__('Author').':</strong> '.$newdata['Author'].'<br>';
			echo '<strong>'.__('Version').':</strong> '.$newdata['Version'].'<br>';
			echo '<em>' . $newdata['Description'] . '</em>';
			?>
			<h2><?php _e('File Changes:'); ?></h2>
			<?php
				$installedFile = ABSPATH . PLUGINDIR . '/';
				$dirname = dirname($_GET['upgrade']);
				if( '.' != $dirname )
					$installedFile .= $dirname;
				else
					$installedFile .= $_GET['upgrade'];

				$diff = folder_diff(ABSPATH . 'wp-content/wpupdate/' . $newdata['Folder'], $installedFile);

				if( ! $diff ){
					_e('Error: Couldnt compare changes.');
				} else {
					foreach( $diff as $fileName => $fileInfo){
						switch($fileInfo['status']) {
							case 'new':
								echo "<span style='color: green'>$fileName (".__('New').")</span><br />";
								break;
							case 'deleted':
								echo "<span style='color: red'>$fileName (".__('Deleted').")</span><br />";
								break;
							case 'changed':
								echo "<span style='color: orange'>$fileName (".__('Changed').")</span><br />";
								break;
							default:
							case 'same':
								echo "<span style='color: black'>$fileName (".__('unChanged').")</span><br />";
								break;
						}
					}//end foreach
				} //end if ! $diff
			?>
			<table align="center">
				<tr>
					<td><p class="submit"><input type="submit" name="submit_cancel" class="delete" value="<?php _e('Cancel'); ?>" /></p></td>
					<td><p class="submit"><input type="submit" name="submit_proceed" value="<?php _e('Proceed &raquo;'); ?>" /></p></td>
				</tr>
			</table>
			<?php
		} else { //if( empty($wp_filesystem->errors)
			?>
			<p class="submit">
				<input type="submit" name="submit" value="<?php _e('Proceed &raquo;'); ?>" />
			</p>
   <?php 
   		} //end if( empty($wp_filesystem->errors)
	} //end if submit_proceed | submit_cancel
   ?>
</div>
</form>