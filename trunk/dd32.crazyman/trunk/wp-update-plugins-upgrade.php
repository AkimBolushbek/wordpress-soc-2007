<?php
	if( !defined('ABSPATH') || ! $wp_update )
		die('Cannot be called directly.');
	require_once('includes/wp-update-filesystem-class.php');
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

$fs_compat = WP_Filesystem_check(false, $opt);
if( is_array($fs_compat) ){ 
var_dump($fs_compat);
?>
<div class="wrap">
	<h2>Extra Filesystem Information is required</h2>
	<?php 
		foreach((array)$fs_compat as $id=>$values){
			if( 'require' == $id )
				continue;
			echo $value . '<br />';
		}
		if( !empty($fs_compat['require']) ){
			foreach((array)$fs_compat['require'] as $id=>$value)
				echo '<b>' . __($value) . ':</b><input type="text" name="required['.$id.']" value="" /><br />';
		}
	?>
</div>
<?php } ?>
<div class="wrap">
	<h2>Currently installed:</h2>
	<strong>Title:</strong> <?php echo $installedInfo['Title']; ?><br/>
	<strong>Author:</strong> <?php echo $installedInfo['Author']; ?><br/>
	<strong>Version:</strong> <?php echo $installedInfo['Version']; ?><br/>
	<em><?php echo $installedInfo['Description']; ?></em>
	<?php
		if( $fs_compat ){
			echo '<h2>' . __('Upgrade file') . ':</h2>';
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
				foreach($plugins as $plugin){
					$newdata = $plugin;
					break;
				}
			} //else, locate the correct one.. blah blah
			
			echo '<strong>' . __('Source') . ':</strong> ' . $file . '<br />';
			echo '<strong>'.__('Title').':</strong> '.$newdata['Title'].'<br>';
			echo '<strong>'.__('Author').':</strong> '.$newdata['Author'].'<br>';
			echo '<strong>'.__('Version').':</strong> '.$newdata['Version'].'<br>';
			echo '<em>' . $newdata['Description'] . '</em>';
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