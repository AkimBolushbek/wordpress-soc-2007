<?php
if( !get_option('update_plugin_search_enable') ){
	echo '<div class="error"><h1>Not Enabled</h1></div>';
	return;
}
require_once('includes/wp-update-class.php');
global $wpupdate;
$wpupdate = new WP_Update;

//$pluginInfo = $wpupdate->getPluginInformationWordPressOrg("http://wordpress.org/extend/plugins/google-sitemap-generator-ultimate-tag-warrior-tags-addon/");
//var_dump($pluginInfo);

//var_dump($tags);
?>
<style type="text/css">
	.taglist p{
		font-size:2em;
	}
	.pluginitem h3{
		text-decoration:underline;
		font-weight:bolder;
		font-size:1.4em;
	}
</style>
<div class="wrap">
<?php
	if( !isset($_POST['term']) ){
?>
	<h2>Search for Plugins via Tags</h2>
	<div class="taglist">
		<p>
<?php
	$tags = $wpupdate->getPluginSearchTags();
	foreach($tags as $tag)
		echo "\t\t\t<a href='?{$tag['name']}' title='{$tag['number']} plugins' rel='tag' style='font-size: {$tag['pointsize']}pt;'>{$tag['name']}</a>\n";
?>
		</p>
	</div>
<?php
} //end if( !isset($_POST['term']) ){
?>
	<h2>Search for Plugins<?php echo isset($_POST['term']) ? ': '.$_POST['term'] : 'via terms'; ?></h2>
	<div class="tagsearch">
		<form method="post" ation="<?php echo ''; ?>">
			<input type="text" name="term" value="<?php echo $_POST['term']; ?>" />
			<input type="submit" name="submit" value="Search" />
		</form>
<?php
	if( isset($_POST['term']) ){
		$term = $_POST['term'];
		echo '<div id="searchresults">';
			$results = $wpupdate->searchPlugins($term);
			foreach((array)$results as $section=>$res){
				if('titlematch' == $section) echo '<h2>Title Matches</h2>';
				if('relevant' == $section) echo '<h2>Relevant Results</h2>';
				foreach((array)$res as $plugin){
					echo '<div class="pluginitem"><p>';
						echo '<h3>'.$plugin['Name'].'</h3>';
						echo nl2br($plugin['Desc']);
						echo "<div class='section'><a href='#'>Install</a> <a href='{$plugin['Uri']}' target='_blank'>{$plugin['Name']} on WordPress.Org</a></div>\n\n";
					echo '</p></div>';
				}
			}
		echo '</div>';
	}
?>
	</div>
</div>