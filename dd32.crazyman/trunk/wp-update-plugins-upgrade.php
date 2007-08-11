<?php
if( !defined('ABSPATH') || ! $wp_update )
	die('Cannot be called directly.');
require_once('includes/wp-update-filesystem.php');
$installedInfo = wpupdate_get_plugin_data(ABSPATH . PLUGINDIR . '/' . $_GET['upgrade']);
$action = array();
foreach($_GET as $key=>$val)
	$action[] = $key .'='. urlencode($val);
?>
<form action="<?php echo $pagenow . '?' . implode('&',$action); ?>" method="post">
<?php
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
var_dump($wp_filesystem->errors);
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
	<h2><?php _e('Currently installed:'); ?></h2>
	<strong><?php _e('Title:'); ?></strong> <?php echo $installedInfo['Title']; ?><br/>
	<strong><?php _e('Author:'); ?></strong> <?php echo $installedInfo['Author']; ?><br/>
	<strong><?php _e('Version:'); ?></strong> <?php echo $installedInfo['Version']; ?><br/>
	<em><?php echo $installedInfo['Description']; ?></em>
	<?php
		if( empty($wp_filesystem->errors) ){
			?>
	<h2><?php _e('Upgrade file:'); ?></h2>
			<?php
			$file = $_GET['url'];
			$fileinfo = pathinfo($file);
			$fileinfo['name'] = $fileinfo['basename'];
			$filename = wpupdate_url_to_file($file);
			
			$messages = $wp_update->installItem($filename, $fileinfo, 'wp-content/wpupdate/');
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
			} //else, locate the correct one.. blah blah

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
				if( ! empty( $dirname ) )
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
								echo "<span style='color: green'>$fileName (New)</span><br />";
								break;
							case 'deleted':
								echo "<span style='color: red'>$fileName (Deleted)</span><br />";
								break;
							case 'changed':
								echo "<span style='color: orange'>$fileName (Changed)</span><br />";
								break;
							default:
							case 'same':
								echo "<span style='color: black'>$fileName (unChanged)</span><br />";
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
		} else {
			?>
			<p class="submit">
				<input type="submit" name="submit" value="<?php _e('Proceed &raquo;'); ?>" />
			</p>
   <?php } ?>
</div>
</form>