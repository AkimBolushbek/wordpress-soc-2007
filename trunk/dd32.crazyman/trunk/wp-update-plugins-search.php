<pre>
<?php
require_once('wp-update-class.php');
global $wpupdate;
$wpupdate = new WP_Update;

$wpupdate->getPluginSearchTags();
?>
</pre>