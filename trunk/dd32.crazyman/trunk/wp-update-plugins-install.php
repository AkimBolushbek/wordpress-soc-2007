<pre>
<?php
require_once('includes/wp-update-class.php');
global $wpupdate;
$wpupdate = new WP_Update;

$pluginInfo = $wpupdate->getPluginInformationWordPressOrg("http://wordpress.org/extend/plugins/google-sitemap-generator-ultimate-tag-warrior-tags-addon/");
var_dump($pluginInfo);

//$tags = $wpupdate->getPluginSearchTags();
//var_dump($tags);
?>
</pre>