<pre>
<?php
require_once('includes/wp-update-class.php');
global $wpupdate;
$wpupdate = new WP_Update;

$tags = $wpupdate->getPluginSearchTags();
var_dump($tags);
?>
</pre>