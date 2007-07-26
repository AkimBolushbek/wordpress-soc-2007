<?php
if( !get_option('update_theme_search_enable') ){
	echo '<div class="error"><h1>Not Enabled</h1></div>';
	return;
}
	
require_once('includes/wp-update-class.php');
require_once('includes/wp-update-functions.php');

global $wpupdate;
$wpupdate = new WP_Update;
?>
<div class="wrap">
<h2>Search Themes</h2>
<form id="searchoptions" name="sortoptions" method="post" action="<?php echo $pagenow . '?page=' . $_GET['page']; ?>">
<?php
	$searchOptions = apply_filters('update_themeSearchOptions', array('sections'=>array()) );
	//Calculate total number of options:
	$options_count = 0;
	foreach($searchOptions['sections'] as $section=>$options){
		$options_count += count($options);
	}
	$average_items_per_section = $options_count / count($searchOptions['sections']);

	foreach($searchOptions['sections'] as $section=>$options){
		if( count($options) > $average_items_per_section ){
			//How many columns?
			$searchOptions['columns'][$section] = ($options_count - 0.4 * count($options) ) / count($searchOptions['sections']); //0.4 = Fudge factor
		} else {
			$searchOptions['columns'][$section] = count($options);
		}
	
	}
	
	echo '<table align="center">';
	echo '<tr>';
	foreach($searchOptions['columns'] as $section => $columns){
		echo '<th';
			if( $columns > 1 )
				echo ' colspan="' . ceil( count($searchOptions['sections'][$section]) / $columns) . '"';
		
		echo '>' . __($section) . '</th>';
	}
	echo '</tr>'; //End of Header
	
	//Content
	echo '<tr>';
	foreach($searchOptions['sections'] as $section => $options){
		$items = array_chunk($options,$searchOptions['columns'][$section]);
		foreach($items as $opts){
			echo '<td valign="top"><p>';
				foreach($opts as $option){
					$checked = in_array(urlencode($option), (array)$_POST['searchOptions']) ? ' checked="checked"' : '';
					echo '<input type="checkbox" name="searchOptions[]" value="'.urlencode($option).'"'.$checked.' />'.__($option).'<br />';
				}
			echo '</p></td>';
		}
	}
	echo '</tr>';
?>
	<tr>
		<td colspan="4">
			<select name='sortby' class='postform'>
				<option value='date'><?php _e('Sort by') ?></option>
				<option value='date'<?php selected('date',$_POST['sortby']) ?>><?php _e('Date') ?></option>
				<option value='title'<?php selected('title',$_POST['sortby']) ?>><?php _e('Title') ?></option>
				<option value='dlcount'<?php selected('dlcount',$_POST['sortby']) ?>><?php _e('Downloads') ?></option>
				<option value='category'<?php selected('category',$_POST['sortby']) ?>><?php _e('Tag') ?></option>
			</select>
			<select name='order' class='postform'>
				<option value='DESC'><?php _e('Order') ?></option>
				<option value='ASC'<?php selected('ASC',$_POST['order']) ?>>ASC</option>
				<option value='DESC'<?php selected('DESC',$_POST['order']) ?>>DESC</option>
			</select>
			<select name='andor' class='postform'>
				<option value='AND'>ANY or ALL</option>
				<option value='OR'<?php selected('OR',$_POST['andor']) ?>>ANY</option>
				<option value='AND'<?php selected('AND',$_POST['andor']) ?>>ALL</option>
			</select>
			<input type="submit" name="submit" value="<?php _e('Search') ?>"  />
		</td>
	</tr>
	<?php
	echo'</table>';
	
?>
</form>
<script type="text/javascript">
//<!CDATA[[
	function loadMore(){
		searchOptions.page++;
		$('#pagenumber').html( searchOptions.page + 1 ); //The Next page after the one we're laoding
		$('#loading-image').show();
		$.post("<?php echo get_option('siteurl'); ?>/wp-content/plugins/wp-update/wp-update-ajax.php?action=themeSearch",
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
<?php
if( isset($_POST['submit']) ){

$taglist = array();
foreach( (array)$_POST['searchOptions'] as $option){
	$taglist[] = urldecode($option);
}
$taglist = implode(', ' . $_POST['andor'] . ' ',$taglist);
?>
<h2><?php _e('Search Results') ?></h2>
<p><?php _e('Search results for Themes tagged with ') ?><strong><span id='taglist'><?php echo $taglist; ?></span></strong></p>
<?php 
	foreach( (array) $_POST['searchOptions'] as $id => $value)
		$_POST['searchOptions'][$id] = urldecode($value);

	$searchResults = $wpupdate->search('themes',$_POST,1); 
?>
<style type="text/css">
	.themeinfo{
		display:inline;
		margin-right:20px;
		width:160px;
		text-align:center;
	}
	.themeinfo span{
		display:inline-block;
		border:thin solid #CCCCCC;
		margin-bottom:40px;
	}
	
</style>
<div id="searchresults">
<?php
if( !isset($searchResults['results']) || empty($searchResults['results']) ){
	_e('Sorry there were no search results');
} else {
	?>
	<script type="text/javascript">
		//<!CDATA[[
			var searchOptions = {"searchOptions": "<?php echo join(',',(array)$searchResults['info']['searchOptions']); ?>",
								"sortby":"<?php echo $searchResults['info']['sortby']; ?>",
								"order":"<?php echo $searchResults['info']['order']; ?>",
								"andor":"<?php echo $searchResults['info']['andor']; ?>",
								"page":<?php echo $searchResults['info']['page']; ?>,
								"pages":<?php echo $searchResults['info']['pages']; ?>};
		//]]>
	</script>
	<?php
	foreach($searchResults['results'] as $theme)
		echo wpupdate_themeSearchHTML($theme);

	if( $searchResults['info']['page'] < $searchResults['info']['pages'] )
		echo '&nbsp;<div class="themeinfo" id="load-more"><span><img style="display:none" src="'.get_option('siteurl'). '/wp-content/plugins/wp-update/images/loading.gif" id="loading-image" /><br/><a href="#load-more" onclick="loadMore()" title="'.$searchResults['info']['pages'].' Pages">'.__('Page').' <b id="pagenumber">2</b> &raquo;</a></span></div>';
}
?>
</div>
<?php
} //end if Search
?>
</div>