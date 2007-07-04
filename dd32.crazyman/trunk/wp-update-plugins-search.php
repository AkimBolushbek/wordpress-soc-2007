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
	.taglist {
		font-size:1.8em; /* overriden by the individual links */
	}
	.searchresults{
	}
	.plugin{
		display:inline;
		width:320px;
		text-align:center;
		vertical-align:top;
	}
	.plugin span{
		display:inline-block;
		border:thin solid #CCCCCC;
		margin-bottom:20px;
		padding:5px;
	}
	#load-more *{
		border:none;
	}
	.pluginitem h3{
		font-size:1em;
	}
	.section{
		font-weight:bold;
		padding-left:50px;
		margin:0px;
	}
</style>
<div class="wrap">
	<div class="searchresults">
	<h2>Search for Plugins<?php echo !empty($searchTerm) ? ': '.$searchTerm : 'via terms'; ?></h2>
		<form method="post" action="<?php echo $url; ?>">
			<input type="text" name="term" value="<?php if( !empty($searchTerm)){ echo attribute_escape($searchTerm);}  ?>" />
			<input type="submit" name="submit" value="Search" />
		</form>
<?php
	if( ! empty($searchTerm) ){
		echo '<div id="searchresults">';
			$results = $wpupdate->search('plugins',array($searchTerm));
			//foreach((array)$results as $section=>$res){
				foreach((array)$results as $plugin){
					echo '<div class="plugin"><span>';
						echo '<h3>'.$plugin['Name'].'</h3>';
						echo '<p>';
							echo wordwrap($plugin['Desc'],25,"<br/>\n");
						echo '</p>';
						echo "<p><a href='#'>Install</a> <a href='{$plugin['Uri']}' target='_blank'>WordPress.Org</a></p>\n\n";
					echo '</span></div> &nbsp; ';
				} //foreach
			//} //foreach
			echo '<div class="plugin" id="load-more"><span><p>&nbsp;<br/><br/><br/></p><p><a href="#more" onclick="loadMore();">Load More Items</a></p></span></div>';
		echo '</div>';
		var_dump($results);
	} //end if empty
?>
	<h2>Search by Tag</h2>
	<div class="taglist">
		<p>
<?php
	$tags = $wpupdate->getPluginSearchTags();
	foreach($tags as $tag)
		echo "<a href='$url&tag={$tag['name']}' title='{$tag['number']} plugins' rel='tag' style='font-size: {$tag['pointsize']}pt;'>{$tag['name']}</a> ";
?>
		</p>
	</div>
</div>