<?php
if( !get_option('update_plugin_search_enable') ){
	echo '<div class="error"><h1>Not Enabled</h1></div>';
	return;
}
require_once('includes/wp-update-class.php');
global $wpupdate,$pagenow;
$wpupdate = new WP_Update;

$url = $pagenow . '?page=' . $_GET['page'];

if( isset($_POST['term']) || isset($_GET['tag']) ){
	if( isset($_POST['term']))
		$searchTerm = $_POST['term'];
	else
		$searchTerm = $_GET['tag'];
}

//$pluginInfo = $wpupdate->getPluginInformationWordPressOrg("http://wordpress.org/extend/plugins/google-sitemap-generator-ultimate-tag-warrior-tags-addon/");
//var_dump($pluginInfo);

//var_dump($tags);
?>
<style type="text/css">
	.taglist p{
		font-size:2em;
	}
	.pluginitem{
		text-indent:30px;
		margin-bottom:0px;
	}
	.pluginitem p{
		background-color:#DEDEDE;
	}
	.pluginitem h3{
		text-decoration:underline;
		font-weight:bolder;
		font-size:1.4em;
	}
	.section{
		font-weight:bold;
		padding-left:50px;
		margin:0px;
	}
</style>
<div class="wrap">
	<h2>Search for Plugins<?php echo !empty($searchTerm) ? ': '.$searchTerm : 'via terms'; ?></h2>
	<div class="tagsearch">
		<form method="post" action="<?php echo $url; ?>">
			<input type="text" name="term" value="<?php if( !empty($searchTerm)){ echo attribute_escape($searchTerm);}  ?>" />
			<input type="submit" name="submit" value="Search" />
		</form>
<?php
	if( ! empty($searchTerm) ){
		echo '<div id="searchresults">';
			$results = $wpupdate->searchPlugins($searchTerm);
			var_dump($results);
			foreach((array)$results as $section=>$res){
				if('titlematch' == $section) echo '<h2>Title Matches</h2>';
				if('relevant' == $section) echo '<h2>Relevant Results</h2>';
				foreach((array)$res as $plugin){
					echo '<div class="pluginitem">';
						echo '<h3>'.$plugin['Name'].'</h3><p>';
						echo nl2br($plugin['Desc']);
						echo "</p><div class='section'><a href='#'>Install</a> <a href='{$plugin['Uri']}' target='_blank'>{$plugin['Name']} on WordPress.Org</a></div>\n\n";
					echo '</div>';
				} //foreach
			} //foreach
		echo '</div>';
	} //end if empty
?>
	</div>

	<h2>Search for Plugins via Tags</h2>
	<div class="taglist">
		<p>
<?php
	$tags = $wpupdate->getPluginSearchTags();
	foreach($tags as $tag)
		echo "\t\t\t<a href='$url&tag={$tag['name']}' title='{$tag['number']} plugins' rel='tag' style='font-size: {$tag['pointsize']}pt;'>{$tag['name']}</a>\n";
?>
		</p>
	</div>
</div>