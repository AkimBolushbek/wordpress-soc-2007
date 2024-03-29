<?php
if( !get_option('update_plugin_search_enable') ){
	echo '<div class="error"><h1>Not Enabled</h1></div>';
	return;
}
require_once('includes/wp-update-class.php');
require_once('includes/wp-update-functions.php');

global $wp_update,$pagenow;
if( ! $wp_update || ! is_object($wp_update) )
	$wp_update = new WP_Update;

if( isset($_POST['term']) || isset($_GET['tag']) ){
	if( isset($_POST['term']))
		$searchTerm = $_POST['term'];
	else
		$tagSearch = $_GET['tag'];
}
?>
<style type="text/css">
	.taglist {
		font-size:1.8em; /* overriden by the individual links, this is mainly to give it a higher lineheight */
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

</style>
<script type="text/javascript">
//<!CDATA[[
	function loadMore(){
		searchOptions.page++;
		$('#loading-image').show();
		$.post("<?php echo get_option('siteurl'); ?>/wp-content/plugins/wp-update/wp-update-ajax.php?action=pluginSearch",
			  searchOptions,
			  function(data){
				$('#load-more').before( data );
				$('#loading-image').hide();
				if( searchOptions.page == searchOptions.pages ){
					$('#load-more').hide();
				}
			  }
			);
		return false;
	}
//]]>
</script>
<div class="wrap">
	<div class="searchresults">
	<h2><?php _e('Search for Plugins'); ?></h2>
		<form method="post" action="<?php echo $pagenow . '?page=' . $_GET['page']; ?>">
			<input type="text" name="term" value="<?php if( !empty($searchTerm)){ echo attribute_escape($searchTerm);}  ?>" />
			<input type="submit" name="submit" value="<?php _e('Search'); ?>" />
		</form>
<?php
	if( !empty($searchTerm) ){
		$resultText = __('Plugin Search: ') . $searchTerm;
		$results = $wp_update->search('plugins',array($searchTerm));
	} elseif (!empty($tagSearch)) {
		$resultText = __('Plugins Tagged: ') . $tagSearch;
		$results = $wp_update->getPluginsByTag($tagSearch);
	}

	if( !empty($searchTerm) || !empty($tagSearch) ){
		if( empty($results)) {
			_e('No results');
		} else {
			echo '<div id="searchresults">';
				echo '<h2>'. $resultText . '</h2>';
				foreach((array)$results['results'] as $plugin)
					echo wpupdate_pluginSearchHTML($plugin);
				
				if( $results['info']['page'] < $results['info']['pages'] ){
					echo '&nbsp;<div class="plugin" id="load-more"><span><img style="display:none" src="'.get_option('siteurl'). '/wp-content/plugins/wp-update/images/loading.gif" id="loading-image" /><br/><a href="#load-more" onclick="return loadMore()">'.__('Next Page').'</a></span></div>';
					?>
					<script type="text/javascript">
						//<!CDATA[[
							var searchOptions = {<?php if( !empty($tagSearch) )
															echo '"tag": "'.$tagSearch.'"';
														else
															echo '"term":"' . $searchTerm . '"'; ?>,
												"page":<?php echo $results['info']['page']; ?>,
												"pages":<?php echo $results['info']['pages']; ?>};
						//]]>
					</script>
					<?php
				}
			echo '</div>';
		} //End results
	} //end if search
	
?>
	<h2><?php _e('Search by Tag'); ?></h2>
	<div class="taglist">
		<p>
<?php
	$tags = $wp_update->getPluginSearchTags();

	$url = $pagenow . '?page=' . $_GET['page'] . '&amp;tag=%s';

	echo wpupdate_generate_tagcloud($tags,'link=' . urlencode($url));
?>
		</p>
	</div>
</div>