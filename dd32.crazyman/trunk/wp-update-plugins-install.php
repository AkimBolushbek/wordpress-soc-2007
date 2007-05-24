<pre>
<?php
if( !get_option('update_install_enable') ){
	echo '<div class="error"><h1>Not Enabled</h1></div>';
	return;
}
	
require_once('includes/wp-update-class.php');
global $wpupdate;
$wpupdate = new WP_Update;

//$pluginInfo = $wpupdate->checkPluginUpdateWordpressOrg("http://wordpress.org/extend/plugins/google-sitemap-generator-ultimate-tag-warrior-tags-addon/",array());
//var_dump($pluginInfo);

//$tags = $wpupdate->getPluginSearchTags();
//var_dump($tags);
?>
</pre>